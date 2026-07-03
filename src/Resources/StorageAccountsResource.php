<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountsByResourceGroup;
use Illuminate\Support\Collection;

final class StorageAccountsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, StorageAccountData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListStorageAccountsByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroupName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => StorageAccountData::fromAzure($item));
    }

    public function account(string $accountName): StorageAccountResource
    {
        return new StorageAccountResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $accountName,
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $accountName,
        string $location,
        string $skuName = 'Standard_LRS',
        string $kind = 'StorageV2',
        array $properties = [],
        array $tags = [],
    ): StorageAccountData {
        return $this->account($accountName)->createOrUpdate($location, $skuName, $kind, $properties, $tags);
    }
}
