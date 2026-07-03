<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions;

use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\ApiManagementRequest;
use Saloon\Enums\Method;

final class ListApimSubscriptionSecrets extends ApiManagementRequest
{
    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serviceName,
        public readonly string $apimSubscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.ApiManagement/service/'.$this->serviceName
            .'/subscriptions/'.$this->apimSubscriptionId.'/listSecrets';
    }
}
