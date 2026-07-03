<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListSchedules extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/schedules';
    }
}
