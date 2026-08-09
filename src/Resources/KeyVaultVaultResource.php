<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\ObservesActualState;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData;
use CodebarAg\MicrosoftAzure\Data\Arm\KeyVaultData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Data\Payload\KeyVaultPayload;
use CodebarAg\MicrosoftAzure\Exceptions\RequestException;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\CreateOrUpdateVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\DeleteVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\GetVault;

final class KeyVaultVaultResource extends Resource
{
    use ObservesActualState;

    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $vaultName,
    ) {
        parent::__construct($client);
    }

    public function get(): KeyVaultData
    {
        $response = $this->sendArm(new GetVault(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->vaultName,
        ));

        return KeyVaultData::fromAzure($this->jsonArray($response));
    }

    /**
     * Actual-state read.
     *
     * Pass $location to distinguish soft-deleted from absent. Key Vault
     * soft-deletes by default: the deleted vault keeps the name for its
     * retention window, ARM 409s every create against it, and no amount of
     * polling frees it — only a purge or the retention period elapsing does.
     * Reported as absent, a caller re-creates forever.
     *
     * Without $location the soft-delete probe is skipped and a deleted vault
     * is indistinguishable from one that never existed — the deleted-vaults
     * surface is scoped per location and there is nothing to guess from.
     */
    public function observe(?string $location = null): ResourceObservationData
    {
        return $this->observeArm(
            new GetVault(
                $this->subscriptionId,
                $this->resourceGroupName,
                $this->vaultName,
            ),
            fn (array $json): KeyVaultData => KeyVaultData::fromAzure($json),
            $location === null ? null : fn (): ?ResourceObservationData => $this->observeSoftDeleted($location),
        );
    }

    /**
     * @return ResourceObservationData|null null when nothing holds the name, so the
     *                                      caller keeps the plain `Absent` observation
     */
    private function observeSoftDeleted(string $location): ?ResourceObservationData
    {
        $deletedVaults = new DeletedVaultsResource($this->client, $this->subscriptionId);

        try {
            $deleted = $deletedVaults->find($location, $this->vaultName);
        } catch (RequestException $exception) {
            // We could not check. "Absent" would be a guess that provokes a
            // create against a name that may well be held — say we don't know.
            return ResourceObservationData::inaccessible($exception->statusCode, $exception->azureMessage);
        }

        if (! $deleted instanceof DeletedVaultData) {
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
        string $tenantId,
        string $skuName = 'standard',
        bool $enableRbacAuthorization = true,
        ?bool $enablePurgeProtection = null,
        array $properties = [],
        array $tags = [],
    ): KeyVaultData {
        $response = $this->sendArm(new CreateOrUpdateVault(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->vaultName,
            new KeyVaultPayload(
                $location,
                $tenantId,
                $skuName,
                $enableRbacAuthorization,
                $enablePurgeProtection,
                $properties,
                $tags,
            ),
        ));

        return KeyVaultData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteVault(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->vaultName,
        ));
    }
}
