<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories;

use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\LogicRequest;
use Saloon\Enums\Method;

final class GetLogicWorkflowTriggerHistory extends LogicRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workflowName,
        public readonly string $triggerName,
        public readonly string $historyName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Logic/workflows/'.$this->workflowName
            .'/triggers'
            .'/'.$this->triggerName
            .'/histories'
            .'/'.$this->historyName;
    }
}
