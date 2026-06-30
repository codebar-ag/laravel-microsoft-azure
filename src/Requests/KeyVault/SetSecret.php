<?php

namespace CodebarAg\MicrosoftAzure\Requests\KeyVault;

use CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class SetSecret extends Request implements HasBody
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

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::KEY_VAULT];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
