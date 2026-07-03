<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults;

use Saloon\Enums\Method;

final class DeleteVault extends KeyVaultVaultsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $vaultName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.KeyVault/vaults/'.$this->vaultName;
    }
}
