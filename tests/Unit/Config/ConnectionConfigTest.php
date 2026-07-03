<?php

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;

it('requires mandatory connection attributes', function (): void {
    expect(fn () => ConnectionConfig::make('broken', ['tenantId' => 'only-tenant']))
        ->toThrow(InvalidArgumentException::class);
});

it('merges snake_case config attributes from the manager', function (): void {
    config()->set('laravel-microsoft-azure.connections.snake', [
        'tenant_id' => 'tenant-snake',
        'client_id' => 'client-snake',
        'client_secret' => 'secret-snake',
        'subscription_id' => 'sub-snake',
        'cache_driver' => 'array',
    ]);

    $config = app(MicrosoftAzureManager::class)->resolveConfig('snake');

    expect($config->tenantId)->toBe('tenant-snake')
        ->and($config->clientId)->toBe('client-snake')
        ->and($config->subscriptionId)->toBe('sub-snake')
        ->and($config->cacheDriver)->toBe('array');
});

it('uses default cache and timeout values', function (): void {
    $config = ConnectionConfig::make('defaults', [
        'tenantId' => 't',
        'clientId' => 'c',
        'clientSecret' => 's',
        'subscriptionId' => 'sub',
    ]);

    expect($config->cacheDriver)->toBe(ConnectionConfig::DEFAULT_CACHE_DRIVER)
        ->and($config->cacheLifetimeInSeconds)->toBe(ConnectionConfig::DEFAULT_CACHE_LIFETIME_IN_SECONDS)
        ->and($config->requestTimeoutInSeconds)->toBe(ConnectionConfig::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS);
});

it('builds configs through the for alias', function (): void {
    $config = ConnectionConfig::for('alias', [
        'tenantId' => 't',
        'clientId' => 'c',
        'clientSecret' => 's',
        'subscriptionId' => 'sub',
    ]);

    expect($config->name)->toBe('alias')
        ->and($config->tenantId)->toBe('t');
});
