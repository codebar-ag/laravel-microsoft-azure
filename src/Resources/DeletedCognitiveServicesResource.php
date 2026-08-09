<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount;
use Illuminate\Support\Collection;
use InvalidArgumentException;

final class DeletedCognitiveServicesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
    ) {
        parent::__construct($client);
    }

    /**
     * Soft-deleted accounts, optionally narrowed to one location.
     *
     * ARM only lists these subscription-wide, so the location filter is
     * applied here rather than in the request. Passing null lists every
     * region — which is what you want when hunting for leaked quota, since a
     * caller usually knows the account name but not where it was created.
     *
     * @return Collection<int, DeletedCognitiveServicesAccountData>
     */
    public function list(?string $location = null): Collection
    {
        $response = $this->sendArm(new ListDeletedCognitiveServicesAccounts($this->subscriptionId));

        $accounts = $this->mapPaginated($response, 'value', fn (array $item) => DeletedCognitiveServicesAccountData::fromAzure($item));

        if ($location === null) {
            return $accounts;
        }

        return $accounts
            ->filter(fn (DeletedCognitiveServicesAccountData $account): bool => $account->location !== null
                && strcasecmp($account->location, $location) === 0)
            ->values();
    }

    /**
     * The soft-deleted account holding $accountName in $location, if one does.
     *
     * A deleted-not-purged account holds more than its name: its model
     * deployments keep counting against the subscription-wide quota for that
     * model until it is purged.
     */
    public function find(string $location, string $accountName): ?DeletedCognitiveServicesAccountData
    {
        return $this->list($location)->first(
            fn (DeletedCognitiveServicesAccountData $account): bool => strcasecmp($account->name, $accountName) === 0,
        );
    }

    /**
     * Permanently remove a soft-deleted account, releasing its model quota.
     *
     * Takes the whole account rather than a name because the purge route needs
     * the resource group, and the only place that survives the group's own
     * deletion is the account's id. Get one from {@see self::find()}.
     */
    public function purge(DeletedCognitiveServicesAccountData $account): void
    {
        if ($account->location === null || $account->resourceGroup === null) {
            throw new InvalidArgumentException(
                "Deleted Cognitive Services account [{$account->name}] cannot be purged: its id [{$account->id}] carries no location and resource group.",
            );
        }

        $this->sendArm(new PurgeDeletedCognitiveServicesAccount(
            $this->subscriptionId,
            $account->location,
            $account->resourceGroup,
            $account->name,
        ));
    }
}
