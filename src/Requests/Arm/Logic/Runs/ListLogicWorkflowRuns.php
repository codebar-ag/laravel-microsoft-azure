<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs;

use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\LogicRequest;
use Saloon\Enums\Method;

final class ListLogicWorkflowRuns extends LogicRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workflowName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Logic/workflows/'.$this->workflowName
            .'/runs';
    }
}
