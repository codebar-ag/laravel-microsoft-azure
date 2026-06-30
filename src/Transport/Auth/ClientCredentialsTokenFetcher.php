<?php

namespace CodebarAg\MicrosoftAzure\Transport\Auth;

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Requests\Auth\ClientCredentialsTokenRequest;
use RuntimeException;
use Saloon\Http\Response;

/**
 * Performs the OAuth client-credentials token exchange for Azure.
 *
 * @internal
 */
final class ClientCredentialsTokenFetcher
{
    public function fetch(ConnectionConfig $config, TokenAudience $audience, ?string $scopeHost = null): AccessTokenData
    {
        $scope = $audience->scope($scopeHost);

        $response = (new ClientCredentialsTokenRequest(
            tenantId: $config->tenantId,
            clientId: $config->clientId,
            clientSecret: $config->clientSecret,
            scope: $scope,
        ))->send();

        if ($response->failed()) {
            throw new RuntimeException($this->failureMessage($response));
        }

        return AccessTokenData::fromAzure($response->json());
    }

    private function failureMessage(Response $response): string
    {
        $body = (string) $response->body();

        /** @var mixed $decoded */
        $decoded = json_decode($body, true);

        if (is_array($decoded)) {
            $message = $decoded['error_description'] ?? $decoded['error'] ?? null;
            if (is_string($message) && $message !== '') {
                return trim((string) preg_replace('/\s\s+/', ' ', $message));
            }
        }

        return 'Azure OAuth token request failed with HTTP '.$response->status().'.';
    }
}
