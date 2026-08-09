<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount;
use Illuminate\Support\Collection;

final class DeletedCognitiveServicesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, DeletedCognitiveServicesAccountData>
     */
    public function list(string $location): Collection
    {
        $response = $this->sendArm(new ListDeletedCognitiveServicesAccounts($this->subscriptionId, $location));

        return $this->mapList($response, 'value', fn (array $item) => DeletedCognitiveServicesAccountData::fromAzure($item));
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

    public function purge(string $location, string $accountName): void
    {
        $this->sendArm(new PurgeDeletedCognitiveServicesAccount($this->subscriptionId, $location, $accountName));
    }
}
