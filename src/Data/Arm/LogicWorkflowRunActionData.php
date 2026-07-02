<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class LogicWorkflowRunActionData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $status = null,
        public ?string $code = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public ?string $trackingId = null,
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
            status: Field::nullableString($properties, 'status'),
            code: Field::nullableString($properties, 'code'),
            startTime: Field::nullableString($properties, 'startTime'),
            endTime: Field::nullableString($properties, 'endTime'),
            trackingId: Field::nullableString($properties, 'trackingId'),
        );
    }
}
