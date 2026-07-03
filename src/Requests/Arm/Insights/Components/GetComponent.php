<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components;

use Saloon\Enums\Method;

final class GetComponent extends AppInsightsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $componentName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Insights/components/'.$this->componentName;
    }
}
