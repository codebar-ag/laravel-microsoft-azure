<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service;

use CodebarAg\MicrosoftAzure\Data\Payload\ApiManagementServicePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\ApiManagementRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateApiManagementService extends ApiManagementRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serviceName,
        public readonly ApiManagementServicePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.ApiManagement/service/'.$this->serviceName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
