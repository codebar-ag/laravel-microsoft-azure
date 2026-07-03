<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

final class SqlServerData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $fullyQualifiedDomainName = null,
        public ?string $state = null,
        public ?ProvisioningState $provisioningState = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $provisioningState = Field::arrNullableString($data, 'properties.provisioningState');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::optionalString($data, 'location'),
            fullyQualifiedDomainName: Field::arrNullableString($data, 'properties.fullyQualifiedDomainName'),
            state: Field::arrNullableString($data, 'properties.state'),
            provisioningState: $provisioningState !== null ? ProvisioningState::tryFrom($provisioningState) : null,
        );
    }
}
