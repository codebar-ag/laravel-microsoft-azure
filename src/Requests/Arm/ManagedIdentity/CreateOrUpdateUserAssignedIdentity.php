<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity;

use CodebarAg\MicrosoftAzure\Data\Payload\UserAssignedIdentityPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateUserAssignedIdentity extends ManagedIdentityRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $identityName,
        public readonly UserAssignedIdentityPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.ManagedIdentity/userAssignedIdentities/'.$this->identityName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
