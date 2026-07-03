<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use Saloon\Enums\Method;

final class GetSubscriptionAlias extends SubscriptionAliasesRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $aliasName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases/'.$this->aliasName;
    }
}
