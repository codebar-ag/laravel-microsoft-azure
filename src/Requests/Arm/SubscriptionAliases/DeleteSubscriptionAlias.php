<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use Saloon\Enums\Method;

final class DeleteSubscriptionAlias extends SubscriptionAliasesRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $aliasName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases/'.$this->aliasName;
    }
}
