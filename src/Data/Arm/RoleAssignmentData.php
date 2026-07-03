<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class RoleAssignmentData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $scope,
        public string $roleDefinitionId,
        public string $principalId,
        public ?string $principalType = null,
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
            scope: Field::arrString($properties, 'scope'),
            roleDefinitionId: Field::arrString($properties, 'roleDefinitionId'),
            principalId: Field::arrString($properties, 'principalId'),
            principalType: Field::nullableString($properties, 'principalType'),
        );
    }
}
