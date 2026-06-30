<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListWorkspacesByResourceGroup extends Request
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

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_LOG_ANALYTICS];
    }
}
