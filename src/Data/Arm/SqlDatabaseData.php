<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use Illuminate\Support\Arr;

final class SqlDatabaseData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $location = null,
        public ?ProvisioningState $status = null,
        public ?string $collation = null,
        public ?string $edition = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = (array) ($data['properties'] ?? []);
        $status = $properties['status'] ?? null;

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            location: $data['location'] ?? null,
            status: is_string($status) ? ProvisioningState::tryFrom($status) : null,
            collation: Arr::get($properties, 'collation'),
            edition: Arr::get($properties, 'currentServiceObjectiveName'),
        );
    }
}
