<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups;

use Saloon\Enums\Method;

final class ListResourceGroups extends ResourceGroupsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/resourcegroups';
    }
}
