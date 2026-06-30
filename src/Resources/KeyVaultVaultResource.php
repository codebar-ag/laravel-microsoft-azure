<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\KeyVaultData;
use CodebarAg\MicrosoftAzure\Data\Payload\KeyVaultPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\CreateOrUpdateVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\DeleteVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\GetVault;

final class KeyVaultVaultResource extends Resource
{
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
