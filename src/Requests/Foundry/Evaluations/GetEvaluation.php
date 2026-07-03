<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetEvaluation extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $evaluationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/evaluations/'.$this->evaluationId;
    }
}
