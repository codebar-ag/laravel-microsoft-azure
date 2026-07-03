<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ApimSubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Payload\ApimSubscriptionPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\CreateOrUpdateApimSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\GetApimSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\ListApimSubscriptions;
use Illuminate\Support\Collection;

final class ApiManagementSubscriptionsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $serviceName,
    ) {
        parent::__construct($client);
    }

    public function create(string $subscriptionId, string $displayName, ?string $scope = null): ApimSubscriptionData
    {
        $response = $this->sendArm(new CreateOrUpdateApimSubscription(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $subscriptionId,
            new ApimSubscriptionPayload($scope ?? '/apis', $displayName),
        ));

        return ApimSubscriptionData::fromAzure($this->jsonArray($response));
    }

    public function get(string $subscriptionId): ApimSubscriptionData
    {
        $response = $this->sendArm(new GetApimSubscription(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $subscriptionId,
        ));

        return ApimSubscriptionData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return Collection<int, ApimSubscriptionData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListApimSubscriptions(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => ApimSubscriptionData::fromAzure($item));
    }

    public function subscription(string $subscriptionId): ApiManagementSubscriptionResource
    {
        return new ApiManagementSubscriptionResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
            $subscriptionId,
        );
    }
}
