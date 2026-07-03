<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Applications;

use CodebarAg\MicrosoftAzure\Data\Payload\CreateApplicationPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateApplication extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly CreateApplicationPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/applications';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
