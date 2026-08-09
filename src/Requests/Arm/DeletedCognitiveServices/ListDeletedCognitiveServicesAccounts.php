<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices;

use Saloon\Enums\Method;

/**
 * Soft-deleted Cognitive Services accounts across the whole subscription.
 *
 * There is no location-scoped list: ARM answers
 * `/providers/Microsoft.CognitiveServices/locations/{location}/deletedAccounts`
 * with 404, so filtering by location is the caller's job (see
 * DeletedCognitiveServicesResource::list()). Only the purge route is
 * location-scoped, which is what makes the asymmetry easy to miss.
 *
 * The response is paginated, and ARM hands out pages whose `value` is empty
 * while still carrying a `nextLink` — a reader that stops at the first page
 * can conclude an account is absent when it is merely on a later one, so this
 * must be consumed through `mapPaginated()`.
 */
final class ListDeletedCognitiveServicesAccounts extends DeletedCognitiveServicesRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.CognitiveServices/deletedAccounts';
    }
}
