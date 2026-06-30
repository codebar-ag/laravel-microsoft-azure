<?php

namespace CodebarAg\MicrosoftAzure\Data\OpenAi;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class OpenAiResponseData extends AzureData
{
    public function __construct(
        public string $id,
        public ?string $model = null,
        public ?string $status = null,
        /** @var array<string, mixed> */
        public array $output = [],
        /** @var array<string, mixed> */
        public array $usage = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            model: Field::arrNullableString($data, 'model'),
            status: Field::arrNullableString($data, 'status'),
            output: Field::mixedArray($data, 'output'),
            usage: Field::mixedArray($data, 'usage'),
        );
    }
}
