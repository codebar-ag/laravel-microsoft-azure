<?php

namespace CodebarAg\MicrosoftAzure\Data\Kudu;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use Illuminate\Support\Arr;

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
        $status = $data['status'] ?? null;

        return new self(
            id: (string) ($data['id'] ?? ''),
            status: is_string($status) ? ProvisioningState::tryFrom($status) : null,
            author: Arr::get($data, 'author'),
            deployer: Arr::get($data, 'deployer'),
            message: Arr::get($data, 'message'),
            startTime: Arr::get($data, 'start_time'),
            endTime: Arr::get($data, 'end_time'),
            complete: Arr::get($data, 'complete'),
            active: Arr::get($data, 'active'),
        );
    }
}
