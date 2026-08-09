<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices;

use Saloon\Enums\Method;

/**
 * Permanently removes a soft-deleted Cognitive Services account.
 *
 * DELETE on the account's deleted-resource path — there is no `/purge` action
 * verb, and the path carries the resource group even though that group has
 * usually been deleted already (the deleted account outlives it, which is why
 * the group name has to be read back off the account's `id` rather than
 * assumed to still exist).
 *
 * Purging is what actually releases the account's model quota. Deleting the
 * resource group only soft-deletes it, and the deployments of a soft-deleted
 * account keep counting against the subscription-wide per-model quota until
 * this runs.
 */
final class PurgeDeletedCognitiveServicesAccount extends DeletedCognitiveServicesRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
        public readonly string $resourceGroup,
        public readonly string $accountName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.CognitiveServices/locations/'.$this->location
            .'/resourceGroups/'.$this->resourceGroup
            .'/deletedAccounts/'.$this->accountName;
    }
}
