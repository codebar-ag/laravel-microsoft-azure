<?php

namespace CodebarAg\MicrosoftAzure\Data\KeyVault;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

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
        $attributes = (array) ($data['attributes'] ?? []);

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? basename((string) ($data['id'] ?? ''))),
            value: Arr::get($data, 'value'),
            contentType: Arr::get($data, 'contentType'),
            createdOn: Arr::get($attributes, 'created'),
            updatedOn: Arr::get($attributes, 'updated'),
            enabled: Arr::get($attributes, 'enabled'),
        );
    }
}
