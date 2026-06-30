<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetSubscriptionAlias extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $aliasName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/providers/Microsoft.Subscription/aliases/'.$this->aliasName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_SUBSCRIPTION_ALIASES];
    }
}
