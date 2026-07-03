<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteSchedule extends FoundryAgentsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $scheduleId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/schedules/'.$this->scheduleId;
    }
}
