<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\ObservesActualState;
use CodebarAg\MicrosoftAzure\Data\Arm\FoundryProjectData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\DeleteFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\ListFoundryProjects;
use Illuminate\Support\Collection;

final class FoundryProjectsResource extends Resource
{
    use ObservesActualState;

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
     * Actual-state read.
     *
     * Projects have no soft-delete surface of their own — deleting one frees
     * the name immediately, and deleting the parent account takes its projects
     * with it into the account's own soft-delete window. Check the account with
     * {@see CognitiveServicesAccountResource::observe()} when a project read
     * comes back absent and you expected otherwise.
     */
    public function observe(string $projectName): ResourceObservationData
    {
        return $this->observeArm(
            new GetFoundryProject(
                $this->subscriptionId,
                $this->resourceGroupName,
                $this->accountName,
                $projectName,
            ),
            fn (array $json): FoundryProjectData => FoundryProjectData::fromAzure($json),
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function createOrUpdate(
        string $projectName,
        string $location,
        array $properties = [],
        ?string $identityType = null,
    ): FoundryProjectData {
        $response = $this->sendArm(new CreateOrUpdateFoundryProject(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $projectName,
            new FoundryProjectPayload($location, $properties, $identityType),
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

    public function connections(string $projectName): ProjectConnectionsResource
    {
        return new ProjectConnectionsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $projectName,
        );
    }
}
