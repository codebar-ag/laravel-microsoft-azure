<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights;

use Saloon\Enums\Method;

final class DeleteWorkspace extends OperationalInsightsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workspaceName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.OperationalInsights/workspaces/'.$this->workspaceName;
    }
}
