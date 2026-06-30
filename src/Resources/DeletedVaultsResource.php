<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\PurgeDeletedVault;
use Illuminate\Support\Collection;

final class DeletedVaultsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, DeletedVaultData>
     */
    public function list(string $location): Collection
    {
        $response = $this->sendArm(new ListDeletedVaults($this->subscriptionId, $location));

        return $this->mapList($response, 'value', fn (array $item) => DeletedVaultData::fromAzure($item));
    }

    public function purge(string $location, string $vaultName): void
    {
        $this->sendArm(new PurgeDeletedVault($this->subscriptionId, $location, $vaultName));
    }
}
