<?php

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;
use CodebarAg\MicrosoftAzure\Resources\GraphResource;

it('registers the manager singleton', function (): void {
    expect(app(MicrosoftAzureManager::class))->toBeInstanceOf(MicrosoftAzureManager::class)
        ->and(app('microsoft-azure.manager'))->toBeInstanceOf(MicrosoftAzureManager::class);
});

it('resolves the default connection from config', function (): void {
    $client = Azure::instance();

    expect($client->name())->toBe('default')
        ->and($client->config->subscriptionId)->toBe('00000000-0000-0000-0000-000000000003');
});

it('builds runtime connections without name caching collisions', function (): void {
    $manager = app(MicrosoftAzureManager::class);

    $a = $manager->connection(testConnectionConfig());
    $b = $manager->connection(testConnectionConfig());

    expect($a)->not->toBe($b)
        ->and($a->config->identifier())->toBe($b->config->identifier());
});

it('accepts a connection config when resolving instances', function (): void {
    $manager = app(MicrosoftAzureManager::class);
    $config = testConnectionConfig();

    expect($manager->instance($config))->toBeInstanceOf(AzureClient::class)
        ->and($manager->instance($config)->config->identifier())->toBe($config->identifier());
});

it('exposes resource gateways through the default connection client', function (): void {
    $manager = app(MicrosoftAzureManager::class);

    expect($manager->graph())->toBeInstanceOf(GraphResource::class)
        ->and($manager->subscriptions())->not->toBeNull();
});

it('throws when connections config is not an array', function (): void {
    config(['laravel-microsoft-azure.connections' => 'invalid']);

    expect(fn () => app(MicrosoftAzureManager::class)->resolveConfig('default'))
        ->toThrow(InvalidArgumentException::class);
});

it('falls back to default cache driver when none is configured', function (): void {
    config([
        'laravel-microsoft-azure.connections.minimal' => [
            'tenant_id' => '00000000-0000-0000-0000-000000000001',
            'client_id' => '00000000-0000-0000-0000-000000000002',
            'client_secret' => 'test-secret',
            'subscription_id' => '00000000-0000-0000-0000-000000000003',
        ],
        'laravel-microsoft-azure.cache.driver' => null,
    ]);

    $config = app(MicrosoftAzureManager::class)->resolveConfig('minimal');

    expect($config->cacheDriver)->toBe('file');
});
