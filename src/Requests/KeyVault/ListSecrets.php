<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListSecrets extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/secrets';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::KEY_VAULT];
    }
}
