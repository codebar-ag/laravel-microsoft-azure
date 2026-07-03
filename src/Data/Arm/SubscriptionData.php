<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionState;

final class SubscriptionData extends AzureData
{
    public function __construct(
        public string $id,
        public string $subscriptionId,
        public ?string $displayName = null,
        public ?SubscriptionState $state = null,
        public ?string $tenantId = null,
        /** @var array<string, mixed> */
        public array $tags = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $state = Field::nullableString($data, 'state');

        return new self(
            id: Field::optionalString($data, 'id'),
            subscriptionId: Field::optionalString($data, 'subscriptionId'),
            displayName: Field::nullableString($data, 'displayName'),
            state: $state !== null ? SubscriptionState::tryFrom($state) : null,
            tenantId: Field::nullableString($data, 'tenantId'),
            tags: Field::mixedArray($data, 'tags'),
        );
    }
}
