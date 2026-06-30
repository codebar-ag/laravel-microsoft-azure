<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

final class DeploymentData extends AzureData
{
    /**
     * @param  array<string, mixed>  $outputs  template outputs keyed by name (each `{ type, value }`)
     * @param  array<string, mixed>  $error  ARM error detail when provisioning failed
     */
    public function __construct(
        public string $id,
        public string $name,
        public ?DeploymentMode $mode = null,
        public ?ProvisioningState $provisioningState = null,
        public ?string $correlationId = null,
        public ?string $timestamp = null,
        public array $outputs = [],
        public array $error = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = Field::properties($data);
        $mode = Field::nullableString($properties, 'mode');
        $state = Field::nullableString($properties, 'provisioningState');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            mode: $mode !== null ? DeploymentMode::tryFrom($mode) : null,
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
            correlationId: Field::nullableString($properties, 'correlationId'),
            timestamp: Field::nullableString($properties, 'timestamp'),
            outputs: Field::mixedArray($properties, 'outputs'),
            error: Field::mixedArray($properties, 'error'),
        );
    }

    /**
     * Read a single template output's `value` by name (e.g. webhook URL, SQL FQDN).
     */
    public function output(string $name): mixed
    {
        $output = $this->outputs[$name] ?? null;

        return is_array($output) ? ($output['value'] ?? null) : null;
    }
}
