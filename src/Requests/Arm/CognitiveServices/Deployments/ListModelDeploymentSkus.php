<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListModelDeploymentSkus extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $deploymentName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.CognitiveServices/accounts/'.$this->accountName
            .'/deployments/'.$this->deploymentName
            .'/skus';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_COGNITIVE_SERVICES];
    }
}
