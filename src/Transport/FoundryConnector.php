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
final class FoundryConnector extends AzureConnector
{
    public function __construct(
        ConnectionConfig $config,
        TokenRepository $tokens,
        ClientCredentialsTokenFetcher $fetcher,
        public readonly string $accountName,
        public readonly string $projectName,
        public readonly ?string $apiKey = null,
    ) {
        parent::__construct($config, $tokens, $fetcher, TokenAudience::CognitiveServicesDataPlane);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://'.$this->accountName.'.services.ai.azure.com/api/projects/'.$this->projectName;
    }

    protected function defaultAuth(): Authenticator
    {
        if ($this->apiKey !== null) {
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

        if ($this->apiKey !== null) {
            $headers['api-key'] = $this->apiKey;
        }

        return $headers;
    }
}
