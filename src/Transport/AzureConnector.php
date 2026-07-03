<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Concerns\ConfiguresAzureTransport;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use Saloon\Contracts\Authenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

/**
 * @internal Consumers use the resource gateways, never this class.
 */
abstract class AzureConnector extends Connector
{
    use ConfiguresAzureTransport;

    public function __construct(
        protected readonly ConnectionConfig $connectionConfig,
        protected readonly TokenRepository $tokens,
        protected readonly ClientCredentialsTokenFetcher $fetcher,
        protected readonly TokenAudience $audience,
        protected readonly ?string $scopeHost = null,
    ) {
        $this->bootAzureTransport();
    }

    public function connectionConfig(): ConnectionConfig
    {
        return $this->connectionConfig;
    }

    /**
     * @return array<string, string>
     */
    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(): array
    {
        return [
            'timeout' => $this->connectionConfig->requestTimeoutInSeconds,
        ];
    }

    protected function defaultAuth(): Authenticator
    {
        return new TokenAuthenticator(
            $this->tokens->accessToken(
                $this->connectionConfig,
                $this->audience,
                $this->scopeHost,
                fn () => $this->fetcher->fetch($this->connectionConfig, $this->audience, $this->scopeHost),
            ),
        );
    }
}
