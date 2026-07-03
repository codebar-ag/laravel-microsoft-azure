<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use CodebarAg\MicrosoftAzure\Data\Payload\SubscriptionAliasPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateSubscriptionAlias extends SubscriptionAliasesRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $aliasName,
        public readonly SubscriptionAliasPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases/'.$this->aliasName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
