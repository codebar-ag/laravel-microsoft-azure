<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

final class DeletedVaultData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $location = null,
        public ?string $deletionDate = null,
        public ?string $scheduledPurgeDate = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = (array) ($data['properties'] ?? []);

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            location: $data['location'] ?? null,
            deletionDate: Arr::get($properties, 'deletionDate'),
            scheduledPurgeDate: Arr::get($properties, 'scheduledPurgeDate'),
        );
    }
}
