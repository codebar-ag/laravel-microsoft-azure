<?php

namespace CodebarAg\MicrosoftAzure\Data\KeyVault;

use CodebarAg\MicrosoftAzure\Data\AzureData;

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
        return new self(
            id: (string) ($data['id'] ?? ''),
            name: isset($data['id']) ? basename((string) $data['id']) : null,
            enabled: $data['attributes']['enabled'] ?? null,
        );
    }
}
