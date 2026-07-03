<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ApiManagementServiceData;
use CodebarAg\MicrosoftAzure\Data\Payload\ApiManagementServicePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service\CreateOrUpdateApiManagementService;

final class ApiManagementResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    public function createOrUpdate(
        string $serviceName,
        string $location,
        string $publisherEmail,
        string $publisherName,
        string $skuName = 'Consumption',
        int $skuCapacity = 0,
    ): ApiManagementServiceData {
        $response = $this->sendArm(new CreateOrUpdateApiManagementService(
            $this->subscriptionId,
            $this->resourceGroupName,
            $serviceName,
            new ApiManagementServicePayload($location, $publisherEmail, $publisherName, $skuName, $skuCapacity),
        ));

        return ApiManagementServiceData::fromAzure($this->jsonArray($response));
    }

    public function service(string $serviceName): ApiManagementServiceResource
    {
        return new ApiManagementServiceResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $serviceName,
        );
    }
}
