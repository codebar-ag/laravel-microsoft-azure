<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class LogicWorkflowData extends AzureData
{
    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $state = null,
        public ?string $provisioningState = null,
        public ?string $accessEndpoint = null,
        public ?string $createdTime = null,
        public ?string $changedTime = null,
        public ?string $version = null,
        public array $definition = [],
        public array $parameters = [],
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
            location: Field::optionalString($data, 'location'),
            state: Field::nullableString($properties, 'state'),
            provisioningState: Field::nullableString($properties, 'provisioningState'),
            accessEndpoint: Field::nullableString($properties, 'accessEndpoint'),
            createdTime: Field::nullableString($properties, 'createdTime'),
            changedTime: Field::nullableString($properties, 'changedTime'),
            version: Field::nullableString($properties, 'version'),
            definition: Field::mixedArray($properties, 'definition'),
            parameters: Field::mixedArray($properties, 'parameters'),
        );
    }
}
