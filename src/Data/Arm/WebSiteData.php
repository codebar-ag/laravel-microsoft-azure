<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

final class WebSiteData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?string $kind = null,
        public ?string $defaultHostName = null,
        public ?string $state = null,
        public ?ProvisioningState $provisioningState = null,
        /** @var array<string, mixed> */
        public array $tags = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $state = Field::arrNullableString($data, 'properties.provisioningState');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            location: Field::optionalString($data, 'location'),
            kind: Field::arrNullableString($data, 'kind'),
            defaultHostName: Field::arrNullableString($data, 'properties.defaultHostName'),
            state: Field::arrNullableString($data, 'properties.state'),
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
            tags: Field::mixedArray($data, 'tags'),
        );
    }
}
