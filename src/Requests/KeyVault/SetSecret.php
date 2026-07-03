<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class SetSecret extends KeyVaultRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $secretName,
        public readonly SetSecretPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/secrets/'.$this->secretName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
