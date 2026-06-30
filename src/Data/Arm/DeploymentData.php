<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use Illuminate\Support\Arr;

final class DeploymentData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?DeploymentMode $mode = null,
        public ?ProvisioningState $provisioningState = null,
        public ?string $correlationId = null,
        public ?string $timestamp = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = (array) ($data['properties'] ?? []);
        $mode = $properties['mode'] ?? null;
        $state = $properties['provisioningState'] ?? null;

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            mode: is_string($mode) ? DeploymentMode::tryFrom($mode) : null,
            provisioningState: is_string($state) ? ProvisioningState::tryFrom($state) : null,
            correlationId: Arr::get($properties, 'correlationId'),
            timestamp: Arr::get($properties, 'timestamp'),
        );
    }
}
