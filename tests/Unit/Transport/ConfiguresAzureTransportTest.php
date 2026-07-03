<?php

use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Transport\ArmConnector;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use Saloon\Enums\Method;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Request;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;

it('retries idempotent requests after fatal transport failures', function (): void {
    $connector = new ArmConnector(
        testConnectionConfig(),
        new EncryptedCacheTokenRepository,
        new ClientCredentialsTokenFetcher,
    );

    $request = new GetResourceGroup('sub-1', 'rg-test');
    $pendingRequest = Mockery::mock(PendingRequest::class);
    $fatal = new FatalRequestException(new RuntimeException('connection reset'), $pendingRequest);

    expect($connector->handleRetry($fatal, $request))->toBeTrue();
});

it('does not retry non-idempotent requests after fatal transport failures', function (): void {
    $connector = new ArmConnector(
        testConnectionConfig(),
        new EncryptedCacheTokenRepository,
        new ClientCredentialsTokenFetcher,
    );

    $request = new class extends Request
    {
        protected Method $method = Method::POST;

        public function resolveEndpoint(): string
        {
            return '/test';
        }
    };

    $pendingRequest = Mockery::mock(PendingRequest::class);
    $fatal = new FatalRequestException(new RuntimeException('connection reset'), $pendingRequest);

    expect($connector->handleRetry($fatal, $request))->toBeFalse();
});

it('retries throttled responses and honors retry-after headers', function (): void {
    config([
        'laravel-microsoft-azure.retry.max_interval_ms' => 5000,
    ]);

    $connector = clientWithSeededToken()->arm();
    $connector->withMockClient(new MockClient([
        GetResourceGroup::class => MockResponse::make(status: 429, headers: ['Retry-After' => '2']),
    ]));

    $request = new GetResourceGroup('sub-1', 'rg-test');
    $response = $connector->send($request);

    expect($response->status())->toBe(429)
        ->and($connector->handleRetry(new RequestException($response), $request))->toBeTrue()
        ->and((new ReflectionProperty($connector, 'retryInterval'))->getValue($connector))->toBe(2000);
});

it('retries server errors for idempotent requests', function (): void {
    $connector = clientWithSeededToken()->arm();
    $connector->withMockClient(new MockClient([
        GetResourceGroup::class => MockResponse::make(status: 503),
    ]));

    $request = new GetResourceGroup('sub-1', 'rg-test');
    $response = $connector->send($request);

    expect($response->status())->toBe(503)
        ->and($connector->handleRetry(new RequestException($response), $request))->toBeTrue();
});

it('skips retry configuration when disabled in config', function (): void {
    config(['laravel-microsoft-azure.retry.enabled' => false]);

    $connector = new ArmConnector(
        testConnectionConfig(),
        new EncryptedCacheTokenRepository,
        new ClientCredentialsTokenFetcher,
    );

    expect((new ReflectionProperty($connector, 'tries'))->getValue($connector))->toBeNull();
});

it('resolves rate limit configuration when enabled', function (): void {
    config([
        'laravel-microsoft-azure.rate_limit.enabled' => true,
        'laravel-microsoft-azure.rate_limit.allow' => 12,
        'laravel-microsoft-azure.rate_limit.per_seconds' => 30,
    ]);

    $connector = new ArmConnector(
        testConnectionConfig(),
        new EncryptedCacheTokenRepository,
        new ClientCredentialsTokenFetcher,
    );

    $resolveLimits = new ReflectionMethod($connector, 'resolveLimits');
    $resolveStore = new ReflectionMethod($connector, 'resolveRateLimitStore');

    $limits = $resolveLimits->invoke($connector);
    $store = $resolveStore->invoke($connector);

    expect($limits)->toHaveCount(1)
        ->and($limits[0]->getAllow())->toBe(12)
        ->and($store)->toBeInstanceOf(LaravelCacheStore::class);
});
