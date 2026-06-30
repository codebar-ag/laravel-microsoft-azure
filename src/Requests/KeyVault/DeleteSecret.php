<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteSecret extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $secretName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/secrets/'.$this->secretName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::KEY_VAULT];
    }
}
