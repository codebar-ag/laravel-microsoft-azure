<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups;
use Illuminate\Support\Collection;

final class ResourceGroupsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
    ) {
        parent::__construct($client);
    }

    public function get(string $resourceGroupName): ResourceGroupData
    {
        $response = $this->sendArm(new GetResourceGroup($this->subscriptionId, $resourceGroupName));

        return ResourceGroupData::fromAzure($response->json());
    }

    /**
     * @param  array<string, mixed>  $properties
     */
    public function createOrUpdate(string $resourceGroupName, string $location, array $properties = []): ResourceGroupData
    {
        $response = $this->sendArm(new CreateOrUpdateResourceGroup(
            $this->subscriptionId,
            $resourceGroupName,
            $location,
            $properties,
        ));

        return ResourceGroupData::fromAzure($response->json());
    }

    public function delete(string $resourceGroupName): void
    {
        $this->sendArm(new DeleteResourceGroup($this->subscriptionId, $resourceGroupName));
    }

    /**
     * @return Collection<int, ResourceGroupData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListResourceGroups($this->subscriptionId));

        return $this->mapList($response, 'value', fn (array $item) => ResourceGroupData::fromAzure($item));
    }
}
