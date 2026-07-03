<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Skills;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListSkills extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/skills';
    }
}
