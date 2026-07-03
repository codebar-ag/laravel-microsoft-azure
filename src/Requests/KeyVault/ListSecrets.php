<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use Saloon\Enums\Method;

final class ListSecrets extends KeyVaultRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/secrets';
    }
}
