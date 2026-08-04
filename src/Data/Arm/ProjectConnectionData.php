<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ProjectConnectionData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $category = null,
        public ?string $authType = null,
        public ?string $target = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            category: Field::arrNullableString($data, 'properties.category'),
            authType: Field::arrNullableString($data, 'properties.authType'),
            target: Field::arrNullableString($data, 'properties.target'),
        );
    }
}
