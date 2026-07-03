<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListToolboxes extends FoundryAgentsRequest implements FoundryFeatureRequest
{
    use HasFoundryFeatures;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/toolboxes';
    }
}
