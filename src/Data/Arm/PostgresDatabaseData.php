<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

/**
 * A database on a PostgreSQL flexible server
 * (`Microsoft.DBforPostgreSQL/flexibleServers/databases`).
 */
final class PostgresDatabaseData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $charset = null,
        public ?string $collation = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            charset: Field::arrNullableString($data, 'properties.charset'),
            collation: Field::arrNullableString($data, 'properties.collation'),
        );
    }
}
