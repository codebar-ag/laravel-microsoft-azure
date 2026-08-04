<?php

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Events\TokenRefreshed;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Event;

it('caches encrypted access tokens and skips fetch on hit', function (): void {
    Event::fake([TokenRefreshed::class]);

    $config = testConnectionConfig();
    $repository = new EncryptedCacheTokenRepository;
    $token = new AccessTokenData(
        accessToken: 'fresh-token',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    );

    $fetchCount = 0;
    $fetch = function () use (&$fetchCount, $token): AccessTokenData {
        $fetchCount++;

        return $token;
    };

    $first = $repository->accessToken($config, TokenAudience::Arm, null, $fetch);
    $second = $repository->accessToken($config, TokenAudience::Arm, null, $fetch);

    expect($first)->toBe('fresh-token')
        ->and($second)->toBe('fresh-token')
        ->and($fetchCount)->toBe(1);

    Event::assertDispatched(TokenRefreshed::class);
});

it('re-fetches when cached payload is corrupt', function (): void {
    $config = testConnectionConfig();
    $repository = new EncryptedCacheTokenRepository;
    $key = 'microsoft-azure.oauth.'.$config->identifier().'.'.TokenAudience::Arm->value;

    Cache::store($config->cacheDriver)->put($key, 'not-encrypted', 3600);

    $token = $repository->accessToken($config, TokenAudience::Arm, null, fn () => new AccessTokenData(
        accessToken: 'recovered',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    ));

    expect($token)->toBe('recovered');
});

it('returns null when decrypted cache payload is not an access token', function (): void {
    $config = testConnectionConfig();
    $repository = new EncryptedCacheTokenRepository;
    $key = 'microsoft-azure.oauth.'.$config->identifier().'.'.TokenAudience::Arm->value;

    Cache::store($config->cacheDriver)->put($key, Crypt::encrypt('plain-string'), 3600);

    $token = $repository->accessToken($config, TokenAudience::Arm, null, fn () => new AccessTokenData(
        accessToken: 'fresh',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    ));

    expect($token)->toBe('fresh');
});

it('uses cache lock when the store supports locking', function (): void {
    $cachePath = sys_get_temp_dir().'/azure-token-cache-'.uniqid('', true);
    mkdir($cachePath, 0777, true);

    config([
        'cache.stores.azure-file' => [
            'driver' => 'file',
            'path' => $cachePath,
        ],
    ]);

    $config = ConnectionConfig::make('test', [
        'tenantId' => '00000000-0000-0000-0000-000000000001',
        'clientId' => '00000000-0000-0000-0000-000000000002',
        'clientSecret' => 'test-secret',
        'subscriptionId' => '00000000-0000-0000-0000-000000000003',
        'cacheDriver' => 'azure-file',
        'cacheLifetimeInSeconds' => 3600,
        'requestTimeoutInSeconds' => 30,
    ]);

    $repository = new EncryptedCacheTokenRepository;
    $token = $repository->accessToken($config, TokenAudience::Graph, null, fn () => new AccessTokenData(
        accessToken: 'locked-token',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    ));

    expect($token)->toBe('locked-token');
});

/**
 * `cacheLifetimeInSeconds` used to be read from ConnectionConfig but never
 * actually consulted — every token was cached for its own ~1 hour Entra
 * expiry regardless. A connection setting this to 0 (a live-integration-test
 * connection forcing a fresh token on every call, so a just-granted RBAC
 * role assignment is exercised immediately rather than failing against a
 * token cached before the grant existed) was silently ignored.
 */
it('does not cache a token when cacheLifetimeInSeconds is 0', function (): void {
    $config = ConnectionConfig::make('test', [
        'tenantId' => '00000000-0000-0000-0000-000000000001',
        'clientId' => '00000000-0000-0000-0000-000000000002',
        'clientSecret' => 'test-secret',
        'subscriptionId' => '00000000-0000-0000-0000-000000000003',
        'cacheDriver' => 'array',
        'cacheLifetimeInSeconds' => 0,
        'requestTimeoutInSeconds' => 30,
    ]);
    $repository = new EncryptedCacheTokenRepository;

    $fetchCount = 0;
    $fetch = function () use (&$fetchCount): AccessTokenData {
        $fetchCount++;

        return new AccessTokenData(
            accessToken: 'token-'.$fetchCount,
            tokenType: 'Bearer',
            expiresIn: 3600,
            expiresAt: Carbon::now()->addHour(),
        );
    };

    $first = $repository->accessToken($config, TokenAudience::FoundryDataPlane, null, $fetch);
    $second = $repository->accessToken($config, TokenAudience::FoundryDataPlane, null, $fetch);

    expect($first)->toBe('token-1')
        ->and($second)->toBe('token-2')
        ->and($fetchCount)->toBe(2);
});

it('caps the cache ttl at cacheLifetimeInSeconds even when the token itself lives longer', function (): void {
    $config = ConnectionConfig::make('test', [
        'tenantId' => '00000000-0000-0000-0000-000000000001',
        'clientId' => '00000000-0000-0000-0000-000000000002',
        'clientSecret' => 'test-secret',
        'subscriptionId' => '00000000-0000-0000-0000-000000000003',
        'cacheDriver' => 'array',
        'cacheLifetimeInSeconds' => 120,
        'requestTimeoutInSeconds' => 30,
    ]);
    $repository = new EncryptedCacheTokenRepository;
    $key = 'microsoft-azure.oauth.'.$config->identifier().'.'.TokenAudience::FoundryDataPlane->value;

    Carbon::setTestNow(Carbon::now());

    $repository->accessToken($config, TokenAudience::FoundryDataPlane, null, fn () => new AccessTokenData(
        accessToken: 'capped-token',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    ));

    expect(Cache::store($config->cacheDriver)->has($key))->toBeTrue();

    // Past the 120s cap but well short of the token's own 3600s expiry — if
    // the cap were still being ignored (the bug), this entry would still be
    // there.
    Carbon::setTestNow(Carbon::now()->addSeconds(130));

    expect(Cache::store($config->cacheDriver)->has($key))->toBeFalse();

    Carbon::setTestNow();
});

it('forgets cached tokens for an audience', function (): void {
    $config = testConnectionConfig();
    $repository = new EncryptedCacheTokenRepository;
    $key = 'microsoft-azure.oauth.'.$config->identifier().'.'.TokenAudience::Graph->value;

    Cache::store($config->cacheDriver)->put(
        $key,
        Crypt::encrypt(new AccessTokenData('x', 'Bearer', 3600, Carbon::now()->addHour())),
        3600,
    );

    $repository->forget($config, TokenAudience::Graph);

    expect(Cache::store($config->cacheDriver)->has($key))->toBeFalse();
});
