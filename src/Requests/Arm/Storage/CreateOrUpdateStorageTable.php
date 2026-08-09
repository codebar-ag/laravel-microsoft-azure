<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Storage;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Table-service table, through the ARM control plane rather than the table data
 * plane — so it is created with the same credential, and in the same pass, as
 * the account's containers and queues.
 *
 * Idempotent: a PUT on an existing table returns it unchanged.
 */
final class CreateOrUpdateStorageTable extends StorageRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $tableName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Storage/storageAccounts/'.$this->accountName
            .'/tableServices/default/tables/'.$this->tableName;
    }

    /**
     * The name is in the path and a table has no other create-time property,
     * but ARM still requires a JSON *object*: an empty PHP array encodes as
     * `[]` and is rejected with "The request cannot be interpreted as valid
     * Json: []" (seen live).
     *
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return ['properties' => (object) []];
    }
}
