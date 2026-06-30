<?php

namespace CodebarAg\MicrosoftAzure\Data\Kudu;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

final class KuduDeploymentData extends AzureData
{
    public function __construct(
        public string $id,
        public ?ProvisioningState $status = null,
        public ?string $author = null,
        public ?string $deployer = null,
        public ?string $message = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public ?bool $complete = null,
        public ?bool $active = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $status = Field::nullableString($data, 'status');

        return new self(
            id: Field::optionalString($data, 'id'),
            status: $status !== null ? ProvisioningState::tryFrom($status) : null,
            author: Field::nullableString($data, 'author'),
            deployer: Field::nullableString($data, 'deployer'),
            message: Field::nullableString($data, 'message'),
            startTime: Field::nullableString($data, 'start_time'),
            endTime: Field::nullableString($data, 'end_time'),
            complete: array_key_exists('complete', $data) && is_bool($data['complete']) ? $data['complete'] : null,
            active: array_key_exists('active', $data) && is_bool($data['active']) ? $data['active'] : null,
        );
    }
}
