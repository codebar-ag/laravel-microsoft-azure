<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowVersionData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\GetLogicWorkflowVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Versions\ListLogicWorkflowVersions;
use Illuminate\Support\Collection;

final class LogicWorkflowVersionsResource extends Resource
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
     * @return Collection<int, LogicWorkflowVersionData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowVersions(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowVersionData::fromAzure($item));
    }

    public function get(string $versionId): LogicWorkflowVersionData
    {
        $response = $this->sendArm(new GetLogicWorkflowVersion(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $versionId,
        ));

        return LogicWorkflowVersionData::fromAzure($this->jsonArray($response));
    }
}
