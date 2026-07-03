<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class ClientCredentialsTokenPayload extends AzurePayload
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $scope,
    ) {}

    public function toAzureBody(): array
    {
        return $this->toFormBody();
    }

    /**
     * @return array<string, mixed>
     */
    public function toFormBody(): array
    {
        return [
            'grant_type' => 'client_credentials',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'scope' => $this->scope,
        ];
    }
}
