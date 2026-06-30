<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;
use Illuminate\Support\Arr;

final class SubscriptionAliasData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $subscriptionId = null,
        public ?ProvisioningState $provisioningState = null,
        public ?string $billingScope = null,
        public ?string $displayName = null,
        public ?SubscriptionWorkload $workload = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = (array) ($data['properties'] ?? []);
        $state = $properties['provisioningState'] ?? null;
        $workload = $properties['workload'] ?? null;

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            subscriptionId: Arr::get($properties, 'subscriptionId'),
            provisioningState: is_string($state) ? ProvisioningState::tryFrom($state) : null,
            billingScope: Arr::get($properties, 'billingScope'),
            displayName: Arr::get($properties, 'displayName'),
            workload: is_string($workload) ? SubscriptionWorkload::tryFrom($workload) : null,
        );
    }
}
