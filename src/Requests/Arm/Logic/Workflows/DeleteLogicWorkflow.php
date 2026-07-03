<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows;

use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\LogicRequest;
use Saloon\Enums\Method;

final class DeleteLogicWorkflow extends LogicRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workflowName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Logic/workflows/'.$this->workflowName;
    }
}
