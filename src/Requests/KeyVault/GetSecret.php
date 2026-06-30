<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetSecret extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $secretName,
        public readonly ?string $version = null,
    ) {}

    public function resolveEndpoint(): string
    {
        $path = '/secrets/'.$this->secretName;

        if ($this->version !== null) {
            $path .= '/'.$this->version;
        }

        return $path;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::KEY_VAULT];
    }
}
