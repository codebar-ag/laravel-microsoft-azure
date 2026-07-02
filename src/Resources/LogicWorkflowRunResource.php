<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\CancelLogicWorkflowRun;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\GetLogicWorkflowRun;

final class LogicWorkflowRunResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $workflowName,
        private readonly string $runName,
    ) {
        parent::__construct($client);
    }

    public function get(): LogicWorkflowRunData
    {
        $response = $this->sendArm(new GetLogicWorkflowRun(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->runName,
        ));

        return LogicWorkflowRunData::fromAzure($this->jsonArray($response));
    }

    public function cancel(): void
    {
        $this->sendArm(new CancelLogicWorkflowRun(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->runName,
        ));
    }

    public function actions(): LogicWorkflowRunActionsResource
    {
        return new LogicWorkflowRunActionsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->runName,
        );
    }
}
