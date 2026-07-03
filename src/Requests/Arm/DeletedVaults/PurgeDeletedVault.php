<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults;

use Saloon\Enums\Method;

final class PurgeDeletedVault extends DeletedVaultsRequest
{
    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
        public readonly string $vaultName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.KeyVault/locations/'.$this->location
            .'/deletedVaults/'.$this->vaultName.'/purge';
    }
}
