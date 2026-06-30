<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use Illuminate\Support\Arr;

final class DeploymentOperationData extends AzureData
{
    public function __construct(
        public string $id,
        public ?string $operationId = null,
        public ?ProvisioningState $provisioningState = null,
        public ?string $statusMessage = null,
        public ?string $targetResource = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = (array) ($data['properties'] ?? []);
        $state = $properties['provisioningState'] ?? null;

        return new self(
            id: (string) ($data['id'] ?? ''),
            operationId: Arr::get($properties, 'operationId'),
            provisioningState: is_string($state) ? ProvisioningState::tryFrom($state) : null,
            statusMessage: Arr::get($properties, 'statusMessage'),
            targetResource: Arr::get($properties, 'targetResource'),
        );
    }
}
