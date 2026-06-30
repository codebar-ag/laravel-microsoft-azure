<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity;

use CodebarAg\MicrosoftAzure\Data\Payload\UserAssignedIdentityPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateUserAssignedIdentity extends Request implements HasBody
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

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_MANAGED_IDENTITY];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
