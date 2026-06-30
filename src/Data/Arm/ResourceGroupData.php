<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use Illuminate\Support\Arr;

final class ResourceGroupData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $location,
        public ?ProvisioningState $provisioningState = null,
        /** @var array<string, mixed> */
        public array $tags = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $state = Arr::get($data, 'properties.provisioningState');

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            location: (string) ($data['location'] ?? ''),
            provisioningState: is_string($state) ? ProvisioningState::tryFrom($state) : null,
            tags: (array) ($data['tags'] ?? []),
        );
    }
}
