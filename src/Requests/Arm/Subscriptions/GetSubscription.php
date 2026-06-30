<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetSubscription extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_SUBSCRIPTIONS];
    }
}
