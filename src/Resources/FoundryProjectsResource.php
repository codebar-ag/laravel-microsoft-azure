<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\FoundryProjectData;
use CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\DeleteFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\ListFoundryProjects;
use Illuminate\Support\Collection;

final class FoundryProjectsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, FoundryProjectData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListFoundryProjects(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => FoundryProjectData::fromAzure($item));
    }

    public function get(string $projectName): FoundryProjectData
    {
        $response = $this->sendArm(new GetFoundryProject(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $projectName,
        ));

        return FoundryProjectData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function createOrUpdate(
        string $projectName,
        string $location,
        array $properties = [],
    ): FoundryProjectData {
        $response = $this->sendArm(new CreateOrUpdateFoundryProject(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $projectName,
            new FoundryProjectPayload($location, $properties),
        ));

        return FoundryProjectData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $projectName): void
    {
        $this->sendArm(new DeleteFoundryProject(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $projectName,
        ));
    }
}
