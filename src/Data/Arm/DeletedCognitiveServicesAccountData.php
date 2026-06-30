<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class DeletedCognitiveServicesAccountData extends AzureData
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
        $properties = Field::properties($data);

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::nullableString($data, 'location'),
            deletionDate: Field::nullableString($properties, 'deletionDate'),
            scheduledPurgeDate: Field::nullableString($properties, 'scheduledPurgeDate'),
        );
    }
}
