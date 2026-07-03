<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions;

use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\LogicRequest;
use Saloon\Enums\Method;

final class GetLogicWorkflowRunAction extends LogicRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workflowName,
        public readonly string $runName,
        public readonly string $actionName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Logic/workflows/'.$this->workflowName
            .'/runs'
            .'/'.$this->runName
            .'/actions'
            .'/'.$this->actionName;
    }
}
