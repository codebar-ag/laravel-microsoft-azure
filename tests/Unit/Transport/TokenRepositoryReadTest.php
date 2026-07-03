<?php

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Tests\Support\FailingLockArrayStore;
use CodebarAg\MicrosoftAzure\Tests\Support\NonLockingArrayStore;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use Illuminate\Cache\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

it('returns null when read decrypts a non-token payload', function (): void {
    $repository = new EncryptedCacheTokenRepository;
    $method = new ReflectionMethod($repository, 'read');
    $method->setAccessible(true);

    $key = 'microsoft-azure.oauth.test';
    Cache::store('array')->put($key, Crypt::encrypt('plain-string'), 3600);

    expect($method->invoke($repository, Cache::store('array'), $key))->toBeNull();
});

it('returns cached token from read without invoking fetch', function (): void {
    $repository = new EncryptedCacheTokenRepository;
    $method = new ReflectionMethod($repository, 'read');
    $method->setAccessible(true);

    $key = 'microsoft-azure.oauth.test';
    Cache::store('array')->put(
        $key,
        Crypt::encrypt(new AccessTokenData('cached', 'Bearer', 3600, Carbon::now()->addHour())),
        3600,
    );

    expect($method->invoke($repository, Cache::store('array'), $key))->toBe('cached');
});

it('returns null when read finds a non-string cache value', function (): void {
    $repository = new EncryptedCacheTokenRepository;
    $method = new ReflectionMethod($repository, 'read');
    $method->setAccessible(true);

    $key = 'microsoft-azure.oauth.test';
    Cache::store('array')->put($key, 123, 3600);

    expect($method->invoke($repository, Cache::store('array'), $key))->toBeNull()
        ->and(Cache::store('array')->has($key))->toBeFalse();
});

it('returns cached token from the refresh closure after lock contention', function (): void {
    $repository = new EncryptedCacheTokenRepository;
    $method = new ReflectionMethod($repository, 'refreshAccessToken');
    $method->setAccessible(true);

    $config = testConnectionConfig();
    $cache = Cache::store($config->cacheDriver);
    $key = 'microsoft-azure.oauth.'.$config->identifier().'.arm';

    $cache->put(
        $key,
        Crypt::encrypt(new AccessTokenData('inside-lock', 'Bearer', 3600, Carbon::now()->addHour())),
        3600,
    );

    $fetchCount = 0;

    expect($method->invoke(
        $repository,
        $cache,
        $key,
        $config,
        function () use (&$fetchCount): AccessTokenData {
            $fetchCount++;

            return new AccessTokenData('fresh', 'Bearer', 3600, Carbon::now()->addHour());
        },
    ))->toBe('inside-lock')
        ->and($fetchCount)->toBe(0);
});

it('refreshes tokens without a lock when the cache store does not support locking', function (): void {
    config(['cache.stores.non-locking' => [
        'driver' => 'non-locking',
    ]]);

    Cache::extend('non-locking', fn () => new Repository(new NonLockingArrayStore));

    $config = ConnectionConfig::make('test', [
        'tenantId' => '00000000-0000-0000-0000-000000000001',
        'clientId' => '00000000-0000-0000-0000-000000000002',
        'clientSecret' => 'test-secret',
        'subscriptionId' => '00000000-0000-0000-0000-000000000003',
        'cacheDriver' => 'non-locking',
        'cacheLifetimeInSeconds' => 3600,
        'requestTimeoutInSeconds' => 30,
    ]);

    $repository = new EncryptedCacheTokenRepository;

    $token = $repository->accessToken($config, TokenAudience::Arm, null, fn () => new AccessTokenData(
        accessToken: 'unlocked',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    ));

    expect($token)->toBe('unlocked');
});

it('throws when lock refresh returns a non-string result', function (): void {
    config(['cache.stores.failing-lock' => [
        'driver' => 'failing-lock',
    ]]);

    Cache::extend('failing-lock', fn () => new Repository(new FailingLockArrayStore));

    $config = ConnectionConfig::make('test', [
        'tenantId' => '00000000-0000-0000-0000-000000000001',
        'clientId' => '00000000-0000-0000-0000-000000000002',
        'clientSecret' => 'test-secret',
        'subscriptionId' => '00000000-0000-0000-0000-000000000003',
        'cacheDriver' => 'failing-lock',
        'cacheLifetimeInSeconds' => 3600,
        'requestTimeoutInSeconds' => 30,
    ]);

    $repository = new EncryptedCacheTokenRepository;

    expect(fn () => $repository->accessToken($config, TokenAudience::Arm, null, fn () => new AccessTokenData(
        accessToken: 'never-used',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    )))->toThrow(RuntimeException::class, 'Failed to refresh Azure OAuth token.');
});
