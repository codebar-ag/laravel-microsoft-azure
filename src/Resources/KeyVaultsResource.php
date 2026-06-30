<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\KeyVaultData;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\ListVaultsByResourceGroup;
use Illuminate\Support\Collection;

final class KeyVaultsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, KeyVaultData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListVaultsByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroupName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => KeyVaultData::fromAzure($item));
    }

    public function vault(string $vaultName): KeyVaultVaultResource
    {
        return new KeyVaultVaultResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $vaultName,
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $vaultName,
        string $location,
        string $tenantId,
        string $skuName = 'standard',
        bool $enableRbacAuthorization = true,
        ?bool $enablePurgeProtection = null,
        array $properties = [],
        array $tags = [],
    ): KeyVaultData {
        return $this->vault($vaultName)->createOrUpdate(
            $location,
            $tenantId,
            $skuName,
            $enableRbacAuthorization,
            $enablePurgeProtection,
            $properties,
            $tags,
        );
    }
}
