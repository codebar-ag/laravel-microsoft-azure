<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Connections;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteConnection extends FoundryAgentsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $connectionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/connections/'.$this->connectionId;
    }
}
