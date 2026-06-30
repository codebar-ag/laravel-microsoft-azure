<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

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
        $properties = Field::properties($data);
        $status = Field::nullableString($properties, 'status');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::nullableString($data, 'location'),
            status: $status !== null ? ProvisioningState::tryFrom($status) : null,
            collation: Field::nullableString($properties, 'collation'),
            edition: Field::nullableString($properties, 'currentServiceObjectiveName'),
        );
    }
}
