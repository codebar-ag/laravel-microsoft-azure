<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use Saloon\Enums\Method;

final class ListSecretVersions extends KeyVaultRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $secretName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/secrets/'.$this->secretName.'/versions';
    }
}
