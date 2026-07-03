<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerHistoryData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\GetLogicWorkflowTriggerHistory;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ListLogicWorkflowTriggerHistories;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\TriggerHistories\ResubmitLogicWorkflowTriggerHistory;
use Illuminate\Support\Collection;

final class LogicWorkflowTriggerHistoriesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $workflowName,
        private readonly string $triggerName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, LogicWorkflowTriggerHistoryData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowTriggerHistories(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowTriggerHistoryData::fromAzure($item));
    }

    public function get(string $historyName): LogicWorkflowTriggerHistoryData
    {
        $response = $this->sendArm(new GetLogicWorkflowTriggerHistory(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
            $historyName,
        ));

        return LogicWorkflowTriggerHistoryData::fromAzure($this->jsonArray($response));
    }

    public function resubmit(string $historyName): void
    {
        $this->sendArm(new ResubmitLogicWorkflowTriggerHistory(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
            $historyName,
        ));
    }
}
