<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals;

use CodebarAg\MicrosoftAzure\Data\Payload\CreateServicePrincipalPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateServicePrincipal extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly CreateServicePrincipalPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/servicePrincipals';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
