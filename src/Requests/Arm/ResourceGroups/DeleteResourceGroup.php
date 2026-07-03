<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups;

use Saloon\Enums\Method;

final class DeleteResourceGroup extends ResourceGroupsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/resourcegroups/'.$this->resourceGroupName;
    }
}
