<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights;

use Saloon\Enums\Method;

final class ListWorkspacesByResourceGroup extends OperationalInsightsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.OperationalInsights/workspaces';
    }
}
