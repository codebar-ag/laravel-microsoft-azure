<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Storage;

use CodebarAg\MicrosoftAzure\Data\Payload\BlobManagementPolicyPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class SetBlobManagementPolicy extends StorageRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly BlobManagementPolicyPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Storage/storageAccounts/'.$this->accountName
            .'/managementPolicies/default';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
