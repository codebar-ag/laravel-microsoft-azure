<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListSubscriptionAliases extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_SUBSCRIPTION_ALIASES];
    }
}
