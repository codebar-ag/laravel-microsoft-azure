<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Threads;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetThread extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $threadId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/threads/'.$this->threadId;
    }
}
