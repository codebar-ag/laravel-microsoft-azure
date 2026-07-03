<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service;

use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\ApiManagementRequest;
use Saloon\Enums\Method;

final class GetApiManagementService extends ApiManagementRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serviceName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.ApiManagement/service/'.$this->serviceName;
    }
}
