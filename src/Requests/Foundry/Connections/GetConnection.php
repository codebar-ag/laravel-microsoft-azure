<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Connections;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetConnection extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $connectionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/connections/'.$this->connectionId;
    }
}
