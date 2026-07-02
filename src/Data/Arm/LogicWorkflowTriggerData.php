<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class LogicWorkflowTriggerData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $state = null,
        public ?string $provisioningState = null,
        public ?string $status = null,
        public ?string $lastExecutionTime = null,
        public ?string $nextExecutionTime = null,
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
            provisioningState: Field::nullableString($properties, 'provisioningState'),
            status: Field::nullableString($properties, 'status'),
            lastExecutionTime: Field::nullableString($properties, 'lastExecutionTime'),
            nextExecutionTime: Field::nullableString($properties, 'nextExecutionTime'),
        );
    }
}
