<?php

namespace CodebarAg\MicrosoftAzure\Transport;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\SharedKeyAuthenticator;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use Saloon\Contracts\Authenticator;

/**
 * @internal
 */
final class StorageQueueConnector extends AzureConnector
{
    public function __construct(
        ConnectionConfig $config,
        TokenRepository $tokens,
        ClientCredentialsTokenFetcher $fetcher,
        public readonly string $accountName,
        public readonly ?string $accountKey = null,
    ) {
        parent::__construct($config, $tokens, $fetcher, TokenAudience::StorageDataPlane);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://'.$this->accountName.'.queue.core.windows.net';
    }

    protected function defaultAuth(): Authenticator
    {
        if ($this->accountKey !== null) {
            return new SharedKeyAuthenticator($this->accountName, $this->accountKey);
        }

        return parent::defaultAuth();
    }

    /**
     * @return array<string, string>
     */
    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/xml',
            'x-ms-version' => ApiVersion::STORAGE_QUEUE->value(),
        ];
    }
}
