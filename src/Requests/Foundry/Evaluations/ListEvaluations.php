<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListEvaluations extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/evaluations';
    }
}
