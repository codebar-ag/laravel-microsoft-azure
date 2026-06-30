<?php

namespace CodebarAg\MicrosoftAzure\Requests\Auth;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\SoloRequest;
use Saloon\Traits\Body\HasFormBody;

final class ClientCredentialsTokenRequest extends SoloRequest implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $tenantId,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $scope,
    ) {}

    public function resolveBaseUrl(): string
    {
        return 'https://login.microsoftonline.com';
    }

    public function resolveEndpoint(): string
    {
        return '/'.$this->tenantId.'/oauth2/v2.0/token';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultBody(): array
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => $this->scope,
        ];
    }
}
