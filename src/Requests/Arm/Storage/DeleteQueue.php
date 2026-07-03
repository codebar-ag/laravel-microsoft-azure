<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Storage;

use Saloon\Enums\Method;

final class DeleteQueue extends StorageRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $queueName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Storage/storageAccounts/'.$this->accountName
            .'/queueServices/default/queues/'.$this->queueName;
    }
}
