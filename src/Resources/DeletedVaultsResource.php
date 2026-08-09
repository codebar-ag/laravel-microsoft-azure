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
     * Soft-deleted vaults, optionally narrowed to one location.
     *
     * ARM only lists these subscription-wide, so the location filter is
     * applied here rather than in the request, and the list is followed across
     * pages — ARM can answer with an empty `value` and a `nextLink`, which a
     * first-page-only read reports as "nothing is soft-deleted".
     *
     * @return Collection<int, DeletedVaultData>
     */
    public function list(?string $location = null): Collection
    {
        $response = $this->sendArm(new ListDeletedVaults($this->subscriptionId));

        $vaults = $this->mapPaginated($response, 'value', fn (array $item) => DeletedVaultData::fromAzure($item));

        if ($location === null) {
            return $vaults;
        }

        return $vaults
            ->filter(fn (DeletedVaultData $vault): bool => $vault->location !== null
                && strcasecmp($vault->location, $location) === 0)
            ->values();
    }

    /**
     * The soft-deleted vault holding $vaultName in $location, if one does.
     *
     * Azure vault names are case-insensitive, so the comparison is too.
     */
    public function find(string $location, string $vaultName): ?DeletedVaultData
    {
        return $this->list($location)
            ->first(fn (DeletedVaultData $vault): bool => strcasecmp($vault->name, $vaultName) === 0);
    }

    public function purge(string $location, string $vaultName): void
    {
        $this->sendArm(new PurgeDeletedVault($this->subscriptionId, $location, $vaultName));
    }
}
