<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class UserAssignedIdentityData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $principalId = null,
        public ?string $clientId = null,
        public ?string $tenantId = null,
        /** @var array<string, mixed> */
        public array $tags = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::optionalString($data, 'location'),
            principalId: Field::arrNullableString($data, 'properties.principalId'),
            clientId: Field::arrNullableString($data, 'properties.clientId'),
            tenantId: Field::arrNullableString($data, 'properties.tenantId'),
            tags: Field::mixedArray($data, 'tags'),
        );
    }
}
