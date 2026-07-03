<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions;

use CodebarAg\MicrosoftAzure\Data\Payload\ApimSubscriptionPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\ApiManagementRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateApimSubscription extends ApiManagementRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serviceName,
        public readonly string $apimSubscriptionId,
        public readonly ApimSubscriptionPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.ApiManagement/service/'.$this->serviceName
            .'/subscriptions/'.$this->apimSubscriptionId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
