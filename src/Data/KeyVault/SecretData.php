<?php

namespace CodebarAg\MicrosoftAzure\Data\KeyVault;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class SecretData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $value = null,
        public ?string $contentType = null,
        public ?string $createdOn = null,
        public ?string $updatedOn = null,
        public ?bool $enabled = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $attributes = Field::mixedArray($data, 'attributes');
        $id = Field::optionalString($data, 'id');

        return new self(
            id: $id,
            name: Field::optionalString($data, 'name', $id !== '' ? basename($id) : ''),
            value: Field::nullableString($data, 'value'),
            contentType: Field::nullableString($data, 'contentType'),
            createdOn: Field::arrNullableString($attributes, 'created'),
            updatedOn: Field::arrNullableString($attributes, 'updated'),
            enabled: array_key_exists('enabled', $attributes) && is_bool($attributes['enabled']) ? $attributes['enabled'] : null,
        );
    }
}
