<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ApiManagementServiceData;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service\DeleteApiManagementService;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service\GetApiManagementService;

final class ApiManagementServiceResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $serviceName,
    ) {
        parent::__construct($client);
    }

    public function get(): ApiManagementServiceData
    {
        $response = $this->sendArm(new GetApiManagementService(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
        ));

        return ApiManagementServiceData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteApiManagementService(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
        ));
    }

    public function subscriptions(): ApiManagementSubscriptionsResource
    {
        return new ApiManagementSubscriptionsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->serviceName,
        );
    }
}
