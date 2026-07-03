<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\NullAuthenticator;

/**
 * @internal
 */
final class FunctionRuntimeConnector extends AzureConnector
{
    public function __construct(
        ConnectionConfig $config,
        TokenRepository $tokens,
        ClientCredentialsTokenFetcher $fetcher,
        public readonly string $appName,
        public readonly ?string $hostKey = null,
    ) {
        $host = $appName.'.azurewebsites.net';

        parent::__construct($config, $tokens, $fetcher, TokenAudience::FunctionRuntime, $host);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://'.$this->appName.'.azurewebsites.net';
    }

    protected function defaultAuth(): Authenticator
    {
        if ($this->hostKey !== null) {
            return new NullAuthenticator;
        }

        return parent::defaultAuth();
    }

    /**
     * @return array<string, string>
     */
    public function defaultHeaders(): array
    {
        $headers = parent::defaultHeaders();

        if ($this->hostKey !== null) {
            $headers['x-functions-key'] = $this->hostKey;
        }

        return $headers;
    }
}
