<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageTableData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageTable;

final class TablesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    public function createOrUpdate(string $tableName): StorageTableData
    {
        $response = $this->sendArm(new CreateOrUpdateStorageTable(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $tableName,
        ));

        return StorageTableData::fromAzure($this->jsonArray($response));
    }
}
