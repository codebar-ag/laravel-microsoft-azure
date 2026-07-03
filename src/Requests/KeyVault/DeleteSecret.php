<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use Saloon\Enums\Method;

final class DeleteSecret extends KeyVaultRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $secretName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/secrets/'.$this->secretName;
    }
}
