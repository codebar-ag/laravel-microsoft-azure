<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Applications;

use CodebarAg\MicrosoftAzure\Data\Payload\AddApplicationPasswordPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class AddApplicationPassword extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $applicationId,
        public readonly AddApplicationPasswordPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/applications/'.$this->applicationId.'/addPassword';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
