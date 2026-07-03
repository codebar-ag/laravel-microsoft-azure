<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class CreateOrUpdateSchedule extends FoundryAgentsRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $scheduleId,
        public readonly AzurePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/schedules/'.$this->scheduleId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
