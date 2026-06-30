<?php

namespace CodebarAg\MicrosoftAzure;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\InteractsWithResources;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use InvalidArgumentException;

final class MicrosoftAzureManager
{
    use InteractsWithResources;

    /** @var array<string, AzureClient> */
    private array $clients = [];

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly TokenRepository $tokens,
        private readonly ClientCredentialsTokenFetcher $fetcher,
    ) {}

    public function instance(string|ConnectionConfig|null $connection = null): AzureClient
    {
        if ($connection instanceof ConnectionConfig) {
            return $this->connection($connection);
        }

        $name = $connection ?? $this->getDefaultConnection();

        return $this->clients[$name] ??= new AzureClient(
            $this->resolveConfig($name),
            $this->tokens,
            $this->fetcher,
        );
    }

    public function connection(ConnectionConfig $config): AzureClient
    {
        return new AzureClient($config, $this->tokens, $this->fetcher);
    }

    public function getDefaultConnection(): string
    {
        return (string) $this->config->get('laravel-microsoft-azure.default', 'default');
    }

    protected function resourceClient(): AzureClient
    {
        return $this->instance();
    }

    public function resolveConfig(string $name): ConnectionConfig
    {
        return ConnectionConfig::make($name, $this->connectionAttributes($name));
    }

    public function forget(?string $name = null): void
    {
        if ($name === null) {
            $this->clients = [];

            return;
        }

        unset($this->clients[$name]);
    }

    /**
     * @return array<string, mixed>
     */
    private function connectionAttributes(string $name): array
    {
        $connections = (array) $this->config->get('laravel-microsoft-azure.connections', []);
        $c = (array) ($connections[$name] ?? []);

        if ($c === []) {
            throw new InvalidArgumentException("Azure connection [{$name}] is not configured.");
        }

        return array_merge($c, [
            'cacheDriver' => $c['cache_driver']
                ?? $this->config->get('laravel-microsoft-azure.cache.driver')
                ?? ConnectionConfig::DEFAULT_CACHE_DRIVER,
            'cacheLifetimeInSeconds' => $c['cache_lifetime']
                ?? $this->config->get('laravel-microsoft-azure.cache.lifetime_in_seconds')
                ?? ConnectionConfig::DEFAULT_CACHE_LIFETIME_IN_SECONDS,
            'requestTimeoutInSeconds' => $c['timeout']
                ?? $this->config->get('laravel-microsoft-azure.request.timeout_in_seconds')
                ?? ConnectionConfig::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS,
            'tenantId' => $c['tenant_id'] ?? $c['tenantId'] ?? null,
            'clientId' => $c['client_id'] ?? $c['clientId'] ?? null,
            'clientSecret' => $c['client_secret'] ?? $c['clientSecret'] ?? null,
            'subscriptionId' => $c['subscription_id'] ?? $c['subscriptionId'] ?? null,
        ]);
    }
}
