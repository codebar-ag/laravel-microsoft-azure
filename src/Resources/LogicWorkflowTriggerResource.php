<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicCallbackUrlData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTrigger;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\GetLogicWorkflowTriggerSchemaJson;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggerCallbackUrl;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ResetLogicWorkflowTrigger;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\RunLogicWorkflowTrigger;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\SetLogicWorkflowTriggerState;

final class LogicWorkflowTriggerResource extends Resource
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

    public function get(): LogicWorkflowTriggerData
    {
        $response = $this->sendArm(new GetLogicWorkflowTrigger(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        ));

        return LogicWorkflowTriggerData::fromAzure($this->jsonArray($response));
    }

    public function run(): void
    {
        $this->sendArm(new RunLogicWorkflowTrigger(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        ));
    }

    public function reset(): void
    {
        $this->sendArm(new ResetLogicWorkflowTrigger(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        ));
    }

    public function listCallbackUrl(): LogicCallbackUrlData
    {
        $response = $this->sendArm(new ListLogicWorkflowTriggerCallbackUrl(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        ));

        return LogicCallbackUrlData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return array<string, mixed>
     */
    public function schemaJson(): array
    {
        $response = $this->sendArm(new GetLogicWorkflowTriggerSchemaJson(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        ));

        return $this->jsonArray($response);
    }

    /**
     * Copy the state from another trigger (source trigger resource id).
     */
    public function setState(string $sourceTriggerId): void
    {
        $this->sendArm(new SetLogicWorkflowTriggerState(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
            new GenericJsonPayload(['source' => ['id' => $sourceTriggerId]]),
        ));
    }

    public function histories(): LogicWorkflowTriggerHistoriesResource
    {
        return new LogicWorkflowTriggerHistoriesResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $this->triggerName,
        );
    }
}
