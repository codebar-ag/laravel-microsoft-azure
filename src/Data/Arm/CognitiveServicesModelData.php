<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class CognitiveServicesModelData extends AzureData
{
    public function __construct(
        public ?string $name = null,
        public ?string $version = null,
        public ?string $format = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            name: Field::arrNullableString($data, 'name'),
            version: Field::arrNullableString($data, 'version'),
            format: Field::arrNullableString($data, 'format'),
        );
    }
}
