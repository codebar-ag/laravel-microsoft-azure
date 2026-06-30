<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use Illuminate\Support\Arr;

final class ModelDeploymentData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $modelFormat = null,
        public ?string $modelName = null,
        public ?string $modelVersion = null,
        public ?string $skuName = null,
        public ?int $skuCapacity = null,
        public ?ProvisioningState $provisioningState = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $state = Field::arrNullableString($data, 'properties.provisioningState');
        $capacityValue = Arr::get($data, 'sku.capacity');
        $skuCapacity = is_numeric($capacityValue) ? (int) $capacityValue : null;

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            modelFormat: Field::arrNullableString($data, 'properties.model.format'),
            modelName: Field::arrNullableString($data, 'properties.model.name'),
            modelVersion: Field::arrNullableString($data, 'properties.model.version'),
            skuName: Field::arrNullableString($data, 'sku.name'),
            skuCapacity: $skuCapacity,
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
        );
    }
}
