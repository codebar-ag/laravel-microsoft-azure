<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Events\AzureResponseReceived;
use CodebarAg\MicrosoftAzure\Security\Redactor;
use Illuminate\Support\Facades\Cache;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\RateLimitPlugin\Contracts\RateLimitStore;
use Saloon\RateLimitPlugin\Limit;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;
use Saloon\RateLimitPlugin\Traits\HasRateLimits;

/**
 * Shared retry, rate-limit, and response-event configuration for Azure connectors.
 */
trait ConfiguresAzureTransport
{
    use HasRateLimits;

    abstract public function connectionConfig(): ConnectionConfig;

    protected function bootAzureTransport(): void
    {
        $this->configureRetries();
        $this->registerResponseEvent();
        $this->useRateLimitPlugin($this->rateLimitConfig()['enabled']);
    }

    public function handleRetry(FatalRequestException|RequestException $exception, Request $request): bool
    {
        $idempotent = in_array($request->getMethod(), [
            Method::GET, Method::HEAD, Method::PUT, Method::DELETE, Method::OPTIONS,
        ], true);

        if ($exception instanceof FatalRequestException) {
            return $idempotent;
        }

        $response = $exception->getResponse();
        $status = $response->status();

        if ($status === 429) {
            $this->applyRetryAfter($response);

            return true;
        }

        if ($status >= 500) {
            return $idempotent;
        }

        return false;
    }

    private function configureRetries(): void
    {
        /** @var array<string, mixed> $retry */
        $retry = Field::fromJson(config('laravel-microsoft-azure.retry', []));

        if (($retry['enabled'] ?? true) === false) {
            return;
        }

        $this->tries = $this->configInt($retry, 'times', 3);
        $this->retryInterval = $this->configInt($retry, 'base_interval_ms', 250);
        $this->useExponentialBackoff = true;
        $this->throwOnMaxTries = false;
    }

    private function applyRetryAfter(Response $response): void
    {
        $retryAfter = $response->header('Retry-After');

        if (is_numeric($retryAfter)) {
            $max = $this->configInt(Field::fromJson(config('laravel-microsoft-azure.retry', [])), 'max_interval_ms', 10000);
            $this->retryInterval = min($max, (int) ((float) $retryAfter * 1000));
        }
    }

    private function registerResponseEvent(): void
    {
        $this->middleware()->onResponse(function (Response $response): void {
            $redactor = new Redactor;
            $psr = $response->getPsrRequest();
            $config = $this->connectionConfig();

            $captureBodies = (bool) config('laravel-microsoft-azure.debug.capture_bodies', false);
            $headers = Field::fromJson($response->headers()->all());

            AzureResponseReceived::dispatch(
                $config->name,
                $psr->getMethod(),
                (string) $psr->getUri()->withQuery(''),
                $response->status(),
                null,
                $this->requestId($response),
                $redactor->redactArray($headers),
                $captureBodies ? $redactor->string((string) $response->body()) : null,
            );
        });
    }

    private function requestId(Response $response): ?string
    {
        foreach (['x-ms-request-id', 'X-Request-Id', 'Request-Id'] as $header) {
            $value = $response->header($header);
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    /**
     * @return array<int, Limit>
     */
    protected function resolveLimits(): array
    {
        $config = $this->rateLimitConfig();

        return [
            Limit::allow($config['allow'])
                ->everySeconds($config['per_seconds'])
                ->name('microsoft-azure:'.$this->connectionConfig()->identifier()),
        ];
    }

    protected function resolveRateLimitStore(): RateLimitStore
    {
        return new LaravelCacheStore(Cache::store($this->connectionConfig()->cacheDriver));
    }

    /**
     * @return array{enabled: bool, allow: int, per_seconds: int}
     */
    private function rateLimitConfig(): array
    {
        /** @var array<string, mixed> $global */
        $global = Field::fromJson(config('laravel-microsoft-azure.rate_limit', []));
        /** @var array<string, mixed> $perConnection */
        $perConnection = Field::fromJson(config(
            'laravel-microsoft-azure.connections.'.$this->connectionConfig()->name.'.rate_limit',
            []
        ));

        $merged = array_merge($global, $perConnection);

        return [
            'enabled' => (bool) ($merged['enabled'] ?? false),
            'allow' => max(1, $this->configInt($merged, 'allow', 60)),
            'per_seconds' => max(1, $this->configInt($merged, 'per_seconds', 60)),
        ];
    }

    /**
     * @param  array<string, mixed>  $config
     */
    private function configInt(array $config, string $key, int $default): int
    {
        $value = $config[$key] ?? $default;

        return is_numeric($value) ? (int) $value : $default;
    }
}
