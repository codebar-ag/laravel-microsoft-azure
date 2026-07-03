<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Deployments;

use CodebarAg\MicrosoftAzure\Data\Payload\DeploymentPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateDeployment extends DeploymentsRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $deploymentName,
        public readonly DeploymentPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourcegroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Resources/deployments/'.$this->deploymentName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
