<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

final class ServerFarmData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $skuName = null,
        public ?string $skuTier = null,
        public ?string $kind = null,
        public bool $reserved = false,
        public ?string $status = null,
        public ?ProvisioningState $provisioningState = null,
        /** @var array<string, mixed> */
        public array $tags = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $state = Field::arrNullableString($data, 'properties.provisioningState');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::optionalString($data, 'location'),
            skuName: Field::arrNullableString($data, 'sku.name'),
            skuTier: Field::arrNullableString($data, 'sku.tier'),
            kind: Field::arrNullableString($data, 'kind'),
            reserved: Field::bool(Field::properties($data), 'reserved'),
            status: Field::arrNullableString($data, 'properties.status'),
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
            tags: Field::mixedArray($data, 'tags'),
        );
    }
}
