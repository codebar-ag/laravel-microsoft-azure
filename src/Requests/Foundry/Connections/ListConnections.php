<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Connections;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListConnections extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/connections';
    }
}
