<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateSubscriptionAlias extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  array<string, mixed>|null  $additionalProperties
     * @param  array<string, string>|null  $tags
     */
    public function __construct(
        public readonly string $aliasName,
        public readonly string $billingScope,
        public readonly string $displayName,
        public readonly SubscriptionWorkload $workload = SubscriptionWorkload::Production,
        public readonly ?string $subscriptionId = null,
        public readonly ?array $additionalProperties = null,
        public readonly ?array $tags = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases/'.$this->aliasName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_SUBSCRIPTION_ALIASES];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
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
