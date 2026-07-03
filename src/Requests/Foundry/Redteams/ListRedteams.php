<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListRedteams extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/redteams';
    }
}
