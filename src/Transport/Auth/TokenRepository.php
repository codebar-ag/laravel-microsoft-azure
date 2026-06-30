<?php

namespace CodebarAg\MicrosoftAzure\Transport\Auth;

use Closure;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;

/**
 * Stores and resolves OAuth access tokens per connection and audience.
 *
 * @internal
 */
interface TokenRepository
{
    /**
     * @param  Closure(): AccessTokenData  $fetch
     */
    public function accessToken(ConnectionConfig $config, TokenAudience $audience, ?string $scopeHost, Closure $fetch): string;

    public function forget(ConnectionConfig $config, TokenAudience $audience, ?string $scopeHost = null): void;
}
