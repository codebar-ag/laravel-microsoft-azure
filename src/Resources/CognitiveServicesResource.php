<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountsByResourceGroup;
use Illuminate\Support\Collection;

final class CognitiveServicesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, CognitiveServicesAccountData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListCognitiveServicesAccountsByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroupName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => CognitiveServicesAccountData::fromAzure($item));
    }

    /**
     * @return Collection<int, CognitiveServicesAccountData>
     */
    public function listAllInSubscription(): Collection
    {
        $response = $this->sendArm(new ListCognitiveServicesAccounts($this->subscriptionId));

        return $this->mapList($response, 'value', fn (array $item) => CognitiveServicesAccountData::fromAzure($item));
    }

    public function account(string $accountName): CognitiveServicesAccountResource
    {
        return new CognitiveServicesAccountResource(
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
        array $properties = [],
        array $tags = [],
        string $kind = 'AIServices',
        string $skuName = 'S0',
    ): CognitiveServicesAccountData {
        return $this->account($accountName)->createOrUpdate($location, $properties, $tags, $kind, $skuName);
    }
}
