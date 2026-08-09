<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\ObservesActualState;
use CodebarAg\MicrosoftAzure\Data\Arm\ApiKeysData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesModelData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Data\Payload\CognitiveServicesAccountPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateKeyPayload;
use CodebarAg\MicrosoftAzure\Exceptions\RequestException;
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
    use ObservesActualState;

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
     * Actual-state read.
     *
     * Pass $location to distinguish soft-deleted from absent. A deleted-not-purged
     * Cognitive Services / Foundry account holds more than its name: its model
     * deployments keep consuming the subscription-wide quota for those models
     * until it is purged. So soft-delete is both a hard failure for a create
     * (the name 409s) and unfinished business for a delete (the quota is still
     * spent) — which is why it is a state of its own rather than a flavour of
     * absent.
     */
    public function observe(?string $location = null): ResourceObservationData
    {
        return $this->observeArm(
            new GetCognitiveServicesAccount(
                $this->subscriptionId,
                $this->resourceGroupName,
                $this->accountName,
            ),
            fn (array $json): CognitiveServicesAccountData => CognitiveServicesAccountData::fromAzure($json),
            $location === null ? null : fn (): ?ResourceObservationData => $this->observeSoftDeleted($location),
        );
    }

    /**
     * @return ResourceObservationData|null null when nothing holds the name, so the
     *                                      caller keeps the plain `Absent` observation
     */
    private function observeSoftDeleted(string $location): ?ResourceObservationData
    {
        $deletedAccounts = new DeletedCognitiveServicesResource($this->client, $this->subscriptionId);

        try {
            $deleted = $deletedAccounts->find($location, $this->accountName);
        } catch (RequestException $exception) {
            return ResourceObservationData::inaccessible($exception->statusCode, $exception->azureMessage);
        }

        if (! $deleted instanceof DeletedCognitiveServicesAccountData) {
            return null;
        }

        return ResourceObservationData::softDeleted(
            id: $deleted->id,
            name: $deleted->name,
            location: $deleted->location,
            scheduledPurgeDate: $deleted->scheduledPurgeDate,
            resource: $deleted,
        );
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
        ?string $identityType = null,
    ): CognitiveServicesAccountData {
        $response = $this->sendArm(new CreateOrUpdateCognitiveServicesAccount(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            new CognitiveServicesAccountPayload($location, $kind, $skuName, $properties, $tags, $identityType),
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
