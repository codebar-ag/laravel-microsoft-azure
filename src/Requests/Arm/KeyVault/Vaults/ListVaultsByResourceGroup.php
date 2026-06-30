<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListVaultsByResourceGroup extends Request
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

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_KEY_VAULT_VAULTS];
    }
}
