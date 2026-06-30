<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetUserAssignedIdentity extends Request
{
    protected Method $method = Method::GET;

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

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_MANAGED_IDENTITY];
    }
}
