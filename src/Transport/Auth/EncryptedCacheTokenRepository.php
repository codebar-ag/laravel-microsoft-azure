<?php

namespace CodebarAg\MicrosoftAzure\Transport\Auth;

use Closure;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Events\TokenRefreshed;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;
use Throwable;

/**
 * Default {@see TokenRepository}: tokens are encrypted with Laravel's `Crypt` and stored in a
 * per-connection-namespaced cache entry, with a cache lock around the refresh.
 *
 * @internal
 */
final class EncryptedCacheTokenRepository implements TokenRepository
{
    private const EXPIRY_SKEW_SECONDS = 60;

    public function accessToken(ConnectionConfig $config, TokenAudience $audience, ?string $scopeHost, Closure $fetch): string
    {
        $cache = Cache::store($config->cacheDriver);
        $key = $this->key($config, $audience, $scopeHost);

        $cached = $this->read($cache, $key);
        if ($cached !== null) {
            return $cached;
        }

        $refresh = fn (): string => $this->refreshAccessToken($cache, $key, $config, $fetch);

        $store = $cache->getStore();
        if ($store instanceof LockProvider) {
            $result = $store->lock($key.':refresh', 15)->block(10, $refresh);
            if (! is_string($result)) {
                throw new RuntimeException('Failed to refresh Azure OAuth token.');
            }

            return $result;
        }

        return $refresh();
    }

    public function forget(ConnectionConfig $config, TokenAudience $audience, ?string $scopeHost = null): void
    {
        Cache::store($config->cacheDriver)->forget($this->key($config, $audience, $scopeHost));
    }

    /**
     * `$config->cacheLifetimeInSeconds` caps how long a token is kept —
     * previously ignored entirely, so every token was cached for its own
     * ~1 hour Entra expiry regardless of what the connection configured.
     * A test connection setting this to 0 (to force a fresh token on every
     * call, e.g. so a just-granted RBAC role assignment is exercised
     * immediately instead of failing against a token cached before the
     * grant existed) was silently not honored: a token cached moments
     * before a role assignment landed stayed cached — and kept being
     * denied — for up to the next hour. `<= 0` means "don't cache at all".
     *
     * @param  Closure(): AccessTokenData  $fetch
     */
    private function refreshAccessToken(
        CacheRepository $cache,
        string $key,
        ConnectionConfig $config,
        Closure $fetch,
    ): string {
        $cached = $this->read($cache, $key);
        if ($cached !== null) {
            return $cached;
        }

        $token = $fetch();

        $ttl = min($token->expiresIn - self::EXPIRY_SKEW_SECONDS, $config->cacheLifetimeInSeconds);

        if ($ttl > 0) {
            $cache->put($key, Crypt::encrypt($token), $ttl);
        }

        TokenRefreshed::dispatch($config->name, $config->tenantId, $config->clientId);

        return $token->accessToken;
    }

    private function read(CacheRepository $cache, string $key): ?string
    {
        if (! $cache->has($key)) {
            return null;
        }

        $encrypted = $cache->get($key);
        if (! is_string($encrypted)) {
            $cache->forget($key);

            return null;
        }

        try {
            $token = Crypt::decrypt($encrypted);
        } catch (Throwable) {
            $cache->forget($key);

            return null;
        }

        return $token instanceof AccessTokenData ? $token->accessToken : null;
    }

    private function key(ConnectionConfig $config, TokenAudience $audience, ?string $scopeHost): string
    {
        $suffix = $scopeHost !== null ? '.'.hash('sha256', $scopeHost) : '';

        return 'microsoft-azure.oauth.'.$config->identifier().'.'.$audience->value.$suffix;
    }
}
