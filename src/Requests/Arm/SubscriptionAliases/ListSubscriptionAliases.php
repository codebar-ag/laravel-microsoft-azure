<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use Saloon\Enums\Method;

final class ListSubscriptionAliases extends SubscriptionAliasesRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases';
    }
}
