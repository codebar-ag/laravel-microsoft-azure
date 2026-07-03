<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class CreateRedteam extends FoundryAgentsRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly AzurePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/redteams';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
