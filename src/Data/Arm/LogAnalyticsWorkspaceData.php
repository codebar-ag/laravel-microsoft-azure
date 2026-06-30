<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class LogAnalyticsWorkspaceData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $customerId = null,
        public ?string $provisioningState = null,
        public ?string $skuName = null,
        public ?int $retentionInDays = null,
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
            location: Field::optionalString($data, 'location'),
            customerId: Field::nullableString($properties, 'customerId'),
            provisioningState: Field::nullableString($properties, 'provisioningState'),
            skuName: Field::arrNullableString($properties, 'sku.name'),
            retentionInDays: isset($properties['retentionInDays']) && is_numeric($properties['retentionInDays'])
                ? (int) $properties['retentionInDays']
                : null,
        );
    }
}
