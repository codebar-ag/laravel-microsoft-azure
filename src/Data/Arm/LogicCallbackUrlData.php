<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class LogicCallbackUrlData extends AzureData
{
    /**
     * @param  array<string, mixed>  $queries
     */
    public function __construct(
        public string $value,
        public ?string $method = null,
        public ?string $basePath = null,
        public ?string $relativePath = null,
        public array $queries = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            value: Field::optionalString($data, 'value'),
            method: Field::nullableString($data, 'method'),
            basePath: Field::nullableString($data, 'basePath'),
            relativePath: Field::nullableString($data, 'relativePath'),
            queries: Field::mixedArray($data, 'queries'),
        );
    }
}
