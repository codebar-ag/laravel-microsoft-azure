<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetRedteam extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $redteamName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/redteams/'.$this->redteamName;
    }
}
