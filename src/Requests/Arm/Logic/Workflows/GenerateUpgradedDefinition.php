<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\LogicRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class GenerateUpgradedDefinition extends LogicRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workflowName,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Logic/workflows/'.$this->workflowName
            .'/generateUpgradedDefinition';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
