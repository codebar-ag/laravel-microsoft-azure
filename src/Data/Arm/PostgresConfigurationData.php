<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

/**
 * A server parameter on a PostgreSQL flexible server
 * (`Microsoft.DBforPostgreSQL/flexibleServers/configurations`) — the ARM
 * surface for what `postgresql.conf` holds on a self-managed instance
 * (`require_secure_transport`, `azure.extensions`, `max_connections`, …).
 *
 * `isDynamicConfig` is the one to check before expecting a change to take
 * effect: `false` means the parameter is static and the server must be
 * restarted before the new value applies.
 */
final class PostgresConfigurationData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $value = null,
        public ?string $defaultValue = null,
        public ?string $dataType = null,
        public ?string $allowedValues = null,
        public ?string $source = null,
        public bool $isDynamicConfig = false,
        public bool $isReadOnly = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = Field::properties($data);

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            value: Field::nullableString($properties, 'value'),
            defaultValue: Field::nullableString($properties, 'defaultValue'),
            dataType: Field::nullableString($properties, 'dataType'),
            allowedValues: Field::nullableString($properties, 'allowedValues'),
            source: Field::nullableString($properties, 'source'),
            isDynamicConfig: Field::bool($properties, 'isDynamicConfig'),
            isReadOnly: Field::bool($properties, 'isReadOnly'),
        );
    }

    /**
     * Whether applying a new value to this parameter requires a server restart.
     */
    public function requiresRestart(): bool
    {
        return ! $this->isDynamicConfig;
    }
}
