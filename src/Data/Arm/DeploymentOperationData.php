<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

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
        $properties = Field::properties($data);
        $state = Field::nullableString($properties, 'provisioningState');

        return new self(
            id: Field::optionalString($data, 'id'),
            operationId: Field::nullableString($properties, 'operationId'),
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
            statusMessage: Field::nullableString($properties, 'statusMessage'),
            targetResource: Field::nullableString($properties, 'targetResource'),
        );
    }
}
