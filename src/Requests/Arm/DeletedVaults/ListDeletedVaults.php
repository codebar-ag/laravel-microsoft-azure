<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults;

use Saloon\Enums\Method;

/**
 * Soft-deleted key vaults across the whole subscription.
 *
 * As with deleted Cognitive Services accounts, ARM serves no location-scoped
 * list — `/providers/Microsoft.KeyVault/locations/{location}/deletedVaults`
 * answers 404 — so filtering by location belongs to the caller (see
 * DeletedVaultsResource::list()). The purge route below IS location-scoped,
 * which is what makes the asymmetry easy to get wrong.
 */
final class ListDeletedVaults extends DeletedVaultsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.KeyVault/deletedVaults';
    }
}
