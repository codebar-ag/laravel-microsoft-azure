<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups;

use Saloon\Enums\Method;

final class GetResourceGroup extends ResourceGroupsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/resourcegroups/'.$this->resourceGroupName;
    }
}
