<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\UserAssignedIdentityData;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\ListUserAssignedIdentitiesByResourceGroup;
use Illuminate\Support\Collection;

final class ManagedIdentitiesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, UserAssignedIdentityData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListUserAssignedIdentitiesByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroupName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => UserAssignedIdentityData::fromAzure($item));
    }

    public function identity(string $identityName): UserAssignedIdentityResource
    {
        return new UserAssignedIdentityResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $identityName,
        );
    }

    /**
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $identityName,
        string $location,
        array $tags = [],
    ): UserAssignedIdentityData {
        return $this->identity($identityName)->createOrUpdate($location, $tags);
    }
}
