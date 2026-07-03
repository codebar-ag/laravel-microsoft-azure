<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;

/**
 * @internal
 */
final class KeyVaultConnector extends AzureConnector
{
    public function __construct(
        ConnectionConfig $config,
        TokenRepository $tokens,
        ClientCredentialsTokenFetcher $fetcher,
        public readonly string $vaultHost,
    ) {
        parent::__construct($config, $tokens, $fetcher, TokenAudience::KeyVault);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://'.$this->vaultHost;
    }
}
