<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Agents;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListAgentContainerOperations extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $agentName,
        public readonly string $version,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents/'.$this->agentName.'/versions/'.$this->version.'/containerOperations';
    }
}
