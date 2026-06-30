<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountKeysData;
use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateStorageKeyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\StorageAccountPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\DeleteStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\GetStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\RegenerateStorageAccountKey;

final class StorageAccountResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    public function get(): StorageAccountData
    {
        $response = $this->sendArm(new GetStorageAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return StorageAccountData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        string $skuName = 'Standard_LRS',
        string $kind = 'StorageV2',
        array $properties = [],
        array $tags = [],
    ): StorageAccountData {
        $response = $this->sendArm(new CreateOrUpdateStorageAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new StorageAccountPayload($location, $skuName, $kind, $properties, $tags),
        ));

        return StorageAccountData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteStorageAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));
    }

    public function listKeys(): StorageAccountKeysData
    {
        $response = $this->sendArm(new ListStorageAccountKeys(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return StorageAccountKeysData::fromAzure($this->jsonArray($response));
    }

    public function regenerateKey(string $keyName): StorageAccountKeysData
    {
        $response = $this->sendArm(new RegenerateStorageAccountKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new RegenerateStorageKeyPayload($keyName),
        ));

        return StorageAccountKeysData::fromAzure($this->jsonArray($response));
    }

    public function blobContainers(): BlobContainersResource
    {
        return new BlobContainersResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        );
    }
}
