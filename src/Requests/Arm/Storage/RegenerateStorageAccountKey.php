<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Storage;

use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateStorageKeyPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class RegenerateStorageAccountKey extends StorageRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly RegenerateStorageKeyPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Storage/storageAccounts/'.$this->accountName
            .'/regenerateKey';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
