<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Deployments;

use Saloon\Enums\Method;

final class CancelDeployment extends DeploymentsRequest
{
    protected Method $method = Method::POST;

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
            .'/cancel';
    }
}
