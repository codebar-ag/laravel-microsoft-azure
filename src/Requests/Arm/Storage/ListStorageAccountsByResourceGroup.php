<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Storage;

use Saloon\Enums\Method;

final class ListStorageAccountsByResourceGroup extends StorageRequest
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
            .'/providers/Microsoft.Storage/storageAccounts';
    }
}
