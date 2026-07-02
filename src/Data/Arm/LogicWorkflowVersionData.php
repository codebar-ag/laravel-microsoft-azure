<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class LogicWorkflowVersionData extends AzureData
{
    /**
     * @param  array<string, mixed>  $definition
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?string $state = null,
        public ?string $createdTime = null,
        public ?string $changedTime = null,
        public array $definition = [],
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
            state: Field::nullableString($properties, 'state'),
            createdTime: Field::nullableString($properties, 'createdTime'),
            changedTime: Field::nullableString($properties, 'changedTime'),
            definition: Field::mixedArray($properties, 'definition'),
        );
    }
}
