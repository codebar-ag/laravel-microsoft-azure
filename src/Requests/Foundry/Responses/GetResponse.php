<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Responses;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetResponse extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $responseId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/responses/'.$this->responseId;
    }
}
