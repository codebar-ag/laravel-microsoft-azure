<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Data\Payload\ResourceGroupPayload;
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

        return ResourceGroupData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $resourceGroupName,
        string $location,
        array $properties = [],
        array $tags = [],
    ): ResourceGroupData {
        $response = $this->sendArm(new CreateOrUpdateResourceGroup(
            $this->subscriptionId,
            $resourceGroupName,
            new ResourceGroupPayload($location, $properties, $tags),
        ));

        return ResourceGroupData::fromAzure($this->jsonArray($response));
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
