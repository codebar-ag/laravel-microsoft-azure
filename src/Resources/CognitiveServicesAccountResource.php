<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ApiKeysData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesModelData;
use CodebarAg\MicrosoftAzure\Data\Payload\CognitiveServicesAccountPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateKeyPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\CreateOrUpdateCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\DeleteCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\GetCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountModels;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountSkus;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\RegenerateCognitiveServicesAccountKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\UpdateCognitiveServicesAccount;
use Illuminate\Support\Collection;

final class CognitiveServicesAccountResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    public function get(): CognitiveServicesAccountData
    {
        $response = $this->sendArm(new GetCognitiveServicesAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return CognitiveServicesAccountData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        array $properties = [],
        array $tags = [],
        string $kind = 'AIServices',
        string $skuName = 'S0',
    ): CognitiveServicesAccountData {
        $response = $this->sendArm(new CreateOrUpdateCognitiveServicesAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new CognitiveServicesAccountPayload($location, $kind, $skuName, $properties, $tags),
        ));

        return CognitiveServicesAccountData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function update(
        string $location,
        array $properties = [],
        array $tags = [],
        string $kind = 'AIServices',
        string $skuName = 'S0',
    ): CognitiveServicesAccountData {
        $response = $this->sendArm(new UpdateCognitiveServicesAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new CognitiveServicesAccountPayload($location, $kind, $skuName, $properties, $tags),
        ));

        return CognitiveServicesAccountData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteCognitiveServicesAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));
    }

    public function listKeys(): ApiKeysData
    {
        $response = $this->sendArm(new ListCognitiveServicesAccountKeys(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return ApiKeysData::fromAzure($this->jsonArray($response));
    }

    public function regenerateKey(string $keyName): ApiKeysData
    {
        $response = $this->sendArm(new RegenerateCognitiveServicesAccountKey(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new RegenerateKeyPayload($keyName),
        ));

        return ApiKeysData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return Collection<int, CognitiveServicesModelData>
     */
    public function listModels(): Collection
    {
        $response = $this->sendArm(new ListCognitiveServicesAccountModels(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => CognitiveServicesModelData::fromAzure($item));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listSkus(): Collection
    {
        $response = $this->sendArm(new ListCognitiveServicesAccountSkus(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => $item);
    }

    public function projects(): FoundryProjectsResource
    {
        return new FoundryProjectsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        );
    }

    public function deployments(): ModelDeploymentsResource
    {
        return new ModelDeploymentsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        );
    }
}
