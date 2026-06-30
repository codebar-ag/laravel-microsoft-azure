<?php

namespace CodebarAg\MicrosoftAzure\Data\KeyVault;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class SecretIdentifierData extends AzureData
{
    public function __construct(
        public string $id,
        public ?string $name = null,
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
            name: $id !== '' ? basename($id) : null,
            enabled: array_key_exists('enabled', $attributes) && is_bool($attributes['enabled']) ? $attributes['enabled'] : null,
        );
    }
}
