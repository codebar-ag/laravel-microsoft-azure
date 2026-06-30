<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionState;
use Illuminate\Support\Arr;

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
        $state = $data['state'] ?? null;

        return new self(
            id: (string) ($data['id'] ?? ''),
            subscriptionId: (string) ($data['subscriptionId'] ?? ''),
            displayName: Arr::get($data, 'displayName'),
            state: is_string($state) ? SubscriptionState::tryFrom($state) : null,
            tenantId: Arr::get($data, 'tenantId'),
            tags: (array) ($data['tags'] ?? []),
        );
    }
}
