<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults;

use Saloon\Enums\Method;

final class ListDeletedVaults extends DeletedVaultsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.KeyVault/locations/'.$this->location.'/deletedVaults';
    }
}
