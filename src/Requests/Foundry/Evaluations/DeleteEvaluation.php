<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteEvaluation extends FoundryAgentsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $evaluationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/evaluations/'.$this->evaluationId;
    }
}
