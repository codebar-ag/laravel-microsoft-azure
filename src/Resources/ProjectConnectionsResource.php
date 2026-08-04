<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ProjectConnectionData;
use CodebarAg\MicrosoftAzure\Data\Payload\ProjectConnectionPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\CreateOrUpdateProjectConnection;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\DeleteProjectConnection;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\GetProjectConnection;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\ListProjectConnections;
use Illuminate\Support\Collection;

final class ProjectConnectionsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
        private readonly string $projectName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, ProjectConnectionData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListProjectConnections(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $this->projectName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => ProjectConnectionData::fromAzure($item));
    }

    public function get(string $connectionName): ProjectConnectionData
    {
        $response = $this->sendArm(new GetProjectConnection(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $this->projectName,
            $connectionName,
        ));

        return ProjectConnectionData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(string $connectionName, ProjectConnectionPayload $payload): ProjectConnectionData
    {
        $response = $this->sendArm(new CreateOrUpdateProjectConnection(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $this->projectName,
            $connectionName,
            $payload,
        ));

        return ProjectConnectionData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $connectionName): void
    {
        $this->sendArm(new DeleteProjectConnection(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $this->projectName,
            $connectionName,
        ));
    }
}
