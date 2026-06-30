<?php

namespace CodebarAg\MicrosoftAzure\Config;

use InvalidArgumentException;

/**
 * Typed, validated configuration for a single Azure connection (tenant/environment).
 */
final class ConnectionConfig
{
    public const DEFAULT_CACHE_DRIVER = 'file';

    public const DEFAULT_CACHE_LIFETIME_IN_SECONDS = 3600;

    public const DEFAULT_REQUEST_TIMEOUT_IN_SECONDS = 60;

    public function __construct(
        public readonly string $name,
        public readonly string $tenantId,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $subscriptionId,
        public readonly string $cacheDriver,
        public readonly int $cacheLifetimeInSeconds,
        public readonly int $requestTimeoutInSeconds,
    ) {}

    /**
     * Stable per-connection identifier — used for cache/token-store namespacing.
     */
    public function identifier(): string
    {
        return hash('sha256', implode('|', [
            $this->name,
            $this->tenantId,
            $this->clientId,
            $this->subscriptionId,
        ]));
    }

    /**
     * Build from a fully-merged attribute array.
     *
     * @param  array<string, mixed>  $attrs
     */
    public static function for(string $name, array $attrs): self
    {
        return self::make($name, $attrs);
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    public static function make(string $name, array $attrs): self
    {
        return new self(
            name: $name,
            tenantId: self::required($name, $attrs, 'tenantId'),
            clientId: self::required($name, $attrs, 'clientId'),
            clientSecret: self::required($name, $attrs, 'clientSecret'),
            subscriptionId: self::required($name, $attrs, 'subscriptionId'),
            cacheDriver: (string) ($attrs['cacheDriver'] ?? self::DEFAULT_CACHE_DRIVER),
            cacheLifetimeInSeconds: (int) ($attrs['cacheLifetimeInSeconds'] ?? self::DEFAULT_CACHE_LIFETIME_IN_SECONDS),
            requestTimeoutInSeconds: (int) ($attrs['requestTimeoutInSeconds'] ?? self::DEFAULT_REQUEST_TIMEOUT_IN_SECONDS),
        );
    }

    /**
     * @param  array<string, mixed>  $attrs
     */
    private static function required(string $name, array $attrs, string $key): string
    {
        $value = $attrs[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw new InvalidArgumentException(
                "Azure connection [{$name}] is missing required config key [{$key}]."
            );
        }

        return $value;
    }
}
