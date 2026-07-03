<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\BlobContainerData;
use CodebarAg\MicrosoftAzure\Data\Payload\BlobContainerPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\BlobManagementPolicyPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateBlobContainer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\SetBlobManagementPolicy;

final class BlobContainersResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    public function createOrUpdate(string $containerName, string $publicAccess = 'None'): BlobContainerData
    {
        $response = $this->sendArm(new CreateOrUpdateBlobContainer(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $containerName,
            new BlobContainerPayload($publicAccess),
        ));

        return BlobContainerData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<int, mixed>  $rules
     */
    public function setManagementPolicy(array $rules): void
    {
        $this->sendArm(new SetBlobManagementPolicy(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new BlobManagementPolicyPayload($rules),
        ));
    }
}
