<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;

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
        $properties = Field::properties($data);
        $state = Field::nullableString($properties, 'provisioningState');
        $workload = Field::nullableString($properties, 'workload');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            subscriptionId: Field::nullableString($properties, 'subscriptionId'),
            provisioningState: $state !== null ? ProvisioningState::tryFrom($state) : null,
            billingScope: Field::nullableString($properties, 'billingScope'),
            displayName: Field::nullableString($properties, 'displayName'),
            workload: $workload !== null ? SubscriptionWorkload::tryFrom($workload) : null,
        );
    }
}
