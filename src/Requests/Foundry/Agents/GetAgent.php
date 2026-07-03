<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Agents;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetAgent extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $agentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents/'.$this->agentId;
    }
}
