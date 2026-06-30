<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Groups;

use CodebarAg\MicrosoftAzure\Data\Payload\CreateGroupPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateGroup extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly CreateGroupPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/groups';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
