<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;

/**
 * @internal
 */
final class KuduConnector extends AzureConnector
{
    public function __construct(
        ConnectionConfig $config,
        TokenRepository $tokens,
        ClientCredentialsTokenFetcher $fetcher,
        public readonly string $appName,
    ) {
        $host = $appName.'.scm.azurewebsites.net';

        parent::__construct($config, $tokens, $fetcher, TokenAudience::Kudu, $host);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://'.$this->appName.'.scm.azurewebsites.net';
    }
}
