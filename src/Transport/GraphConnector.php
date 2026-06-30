<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;

/**
 * @internal
 */
final class GraphConnector extends AzureConnector
{
    public function __construct(
        ConnectionConfig $config,
        TokenRepository $tokens,
        ClientCredentialsTokenFetcher $fetcher,
    ) {
        parent::__construct($config, $tokens, $fetcher, TokenAudience::Graph);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://graph.microsoft.com/v1.0';
    }
}
