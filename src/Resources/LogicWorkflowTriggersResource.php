<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowTriggerData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Triggers\ListLogicWorkflowTriggers;
use Illuminate\Support\Collection;

final class LogicWorkflowTriggersResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $workflowName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, LogicWorkflowTriggerData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowTriggers(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowTriggerData::fromAzure($item));
    }

    public function trigger(string $triggerName): LogicWorkflowTriggerResource
    {
        return new LogicWorkflowTriggerResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $triggerName,
        );
    }
}
