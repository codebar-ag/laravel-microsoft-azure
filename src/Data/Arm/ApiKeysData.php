<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ApiKeysData extends AzureData
{
    public function __construct(
        public ?string $key1 = null,
        public ?string $key2 = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            key1: Field::arrNullableString($data, 'key1'),
            key2: Field::arrNullableString($data, 'key2'),
        );
    }
}
