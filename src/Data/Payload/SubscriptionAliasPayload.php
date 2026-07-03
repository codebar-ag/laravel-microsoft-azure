<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;

final class SubscriptionAliasPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>|null  $additionalProperties
     * @param  array<string, string>|null  $tags
     */
    public function __construct(
        public readonly string $billingScope,
        public readonly string $displayName,
        public readonly SubscriptionWorkload $workload = SubscriptionWorkload::Production,
        public readonly ?string $subscriptionId = null,
        public readonly ?array $additionalProperties = null,
        public readonly ?array $tags = null,
    ) {}

    public function toAzureBody(): array
    {
        $properties = [
            'billingScope' => $this->billingScope,
            'displayName' => $this->displayName,
            'workload' => $this->workload->value,
        ];

        if ($this->subscriptionId !== null) {
            $properties['subscriptionId'] = $this->subscriptionId;
        }

        if ($this->additionalProperties !== null || $this->tags !== null) {
            $properties['additionalProperties'] = array_filter([
                ...($this->additionalProperties ?? []),
                'tags' => $this->tags,
            ], fn ($value) => $value !== null);
        }

        return ['properties' => $properties];
    }
}
