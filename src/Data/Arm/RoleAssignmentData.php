<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

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
        $properties = (array) ($data['properties'] ?? []);

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            scope: (string) Arr::get($properties, 'scope', ''),
            roleDefinitionId: (string) Arr::get($properties, 'roleDefinitionId', ''),
            principalId: (string) Arr::get($properties, 'principalId', ''),
            principalType: Arr::get($properties, 'principalType'),
        );
    }
}
