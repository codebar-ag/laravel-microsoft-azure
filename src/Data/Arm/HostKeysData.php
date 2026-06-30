<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class HostKeysData extends AzureData
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public array $properties = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            properties: Field::mixedArray($data, 'properties'),
        );
    }
}
