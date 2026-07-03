<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use Saloon\Enums\Method;

final class GetSecret extends KeyVaultRequest
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
}
