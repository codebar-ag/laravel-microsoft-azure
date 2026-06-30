<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Deployments;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListDeploymentOperations extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $deploymentName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourcegroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Resources/deployments/'.$this->deploymentName
            .'/operations';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DEPLOYMENTS];
    }
}
