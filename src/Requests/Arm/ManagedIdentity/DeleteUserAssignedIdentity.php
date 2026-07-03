<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity;

use Saloon\Enums\Method;

final class DeleteUserAssignedIdentity extends ManagedIdentityRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $identityName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.ManagedIdentity/userAssignedIdentities/'.$this->identityName;
    }
}
