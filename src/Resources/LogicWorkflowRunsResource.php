<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowRunData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Runs\ListLogicWorkflowRuns;
use Illuminate\Support\Collection;

final class LogicWorkflowRunsResource extends Resource
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
     * @return Collection<int, LogicWorkflowRunData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowRuns(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowRunData::fromAzure($item));
    }

    public function run(string $runName): LogicWorkflowRunResource
    {
        return new LogicWorkflowRunResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $runName,
        );
    }
}
