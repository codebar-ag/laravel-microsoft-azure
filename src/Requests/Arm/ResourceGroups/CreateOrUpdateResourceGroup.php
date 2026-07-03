<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups;

use CodebarAg\MicrosoftAzure\Data\Payload\ResourceGroupPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateResourceGroup extends ResourceGroupsRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly ResourceGroupPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/resourcegroups/'.$this->resourceGroupName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
