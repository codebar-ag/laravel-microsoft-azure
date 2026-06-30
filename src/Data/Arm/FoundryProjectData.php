<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

final class FoundryProjectData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?ProvisioningState $provisioningState = null,
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
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
        );
    }
}
