<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class RoleDefinitionData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $roleName = null,
        public ?string $type = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            roleName: Field::arrNullableString($data, 'properties.roleName'),
            type: Field::nullableString($data, 'type'),
        );
    }
}
