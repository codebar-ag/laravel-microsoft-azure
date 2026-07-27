<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

/**
 * An Azure Database for PostgreSQL **flexible** server
 * (`Microsoft.DBforPostgreSQL/flexibleServers`).
 *
 * Note this provider reports readiness on `properties.state`
 * (`Ready`/`Disabled`/`Starting`/`Stopped`/`Updating`/`Dropping`), NOT on a
 * `properties.provisioningState` field the way most ARM providers do — so
 * unlike {@see SqlServerData} there is no ProvisioningState enum here. Poll
 * `state === 'Ready'`.
 */
final class PostgresFlexibleServerData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $fullyQualifiedDomainName = null,
        public ?string $state = null,
        public ?string $version = null,
        public ?string $administratorLogin = null,
        public ?string $skuName = null,
        public ?string $skuTier = null,
        public ?int $storageSizeGB = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        // Field::mixedArray() is plain key access (no dot notation, unlike
        // Field::arrNullableString()), so `properties.storage` has to be
        // walked one level at a time.
        $storage = Field::mixedArray(Field::properties($data), 'storage');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::optionalString($data, 'location'),
            fullyQualifiedDomainName: Field::arrNullableString($data, 'properties.fullyQualifiedDomainName'),
            state: Field::arrNullableString($data, 'properties.state'),
            version: Field::arrNullableString($data, 'properties.version'),
            administratorLogin: Field::arrNullableString($data, 'properties.administratorLogin'),
            skuName: Field::arrNullableString($data, 'sku.name'),
            skuTier: Field::arrNullableString($data, 'sku.tier'),
            storageSizeGB: Field::nullableInt($storage, 'storageSizeGB'),
        );
    }

    /**
     * Whether the server has finished provisioning and accepts connections.
     */
    public function isReady(): bool
    {
        return $this->state === 'Ready';
    }
}
