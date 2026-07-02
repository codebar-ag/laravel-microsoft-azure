<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunActionData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\GetLogicWorkflowRunAction;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActionExpressionTraces;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\RunActions\ListLogicWorkflowRunActions;
use Illuminate\Support\Collection;

final class LogicWorkflowRunActionsResource extends Resource
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

    /**
     * @return Collection<int, LogicWorkflowRunActionData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowRunActions(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->runName,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowRunActionData::fromAzure($item));
    }

    public function get(string $actionName): LogicWorkflowRunActionData
    {
        $response = $this->sendArm(new GetLogicWorkflowRunAction(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->runName,
            $actionName,
        ));

        return LogicWorkflowRunActionData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function expressionTraces(string $actionName): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowRunActionExpressionTraces(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->runName,
            $actionName,
        ));

        return $this->mapList($response, 'inputs', fn (array $item) => $item);
    }
}
