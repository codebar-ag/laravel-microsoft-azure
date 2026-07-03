<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Storage;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateQueue extends StorageRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  array<string, string>  $metadata
     */
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $queueName,
        public readonly array $metadata = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Storage/storageAccounts/'.$this->accountName
            .'/queueServices/default/queues/'.$this->queueName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        // Azure requires `metadata` as a JSON object; an empty PHP array would
        // otherwise serialize as `[]` and be rejected as invalid JSON for this field.
        return ['properties' => ['metadata' => (object) $this->metadata]];
    }
}
