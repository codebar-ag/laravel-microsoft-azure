<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteToolbox extends FoundryAgentsRequest implements FoundryFeatureRequest
{
    use HasFoundryFeatures;

    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $toolboxName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/toolboxes/'.$this->toolboxName;
    }
}
