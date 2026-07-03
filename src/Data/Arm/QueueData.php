<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class QueueData extends AzureData
{
    /**
     * @param  array<string, string>  $metadata
     */
    public function __construct(
        public string $id,
        public string $name,
        public array $metadata = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $metadata = Field::mixedArray(Field::properties($data), 'metadata');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            metadata: array_map(fn (mixed $value) => is_scalar($value) ? (string) $value : '', $metadata),
        );
    }
}
