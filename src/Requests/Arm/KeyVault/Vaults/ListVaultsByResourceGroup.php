<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults;

use Saloon\Enums\Method;

final class ListVaultsByResourceGroup extends KeyVaultVaultsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.KeyVault/vaults';
    }
}
