<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\UserAssignedIdentityData;
use CodebarAg\MicrosoftAzure\Data\Payload\UserAssignedIdentityPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\CreateOrUpdateUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\DeleteUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\GetUserAssignedIdentity;

final class UserAssignedIdentityResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $identityName,
    ) {
        parent::__construct($client);
    }

    public function get(): UserAssignedIdentityData
    {
        $response = $this->sendArm(new GetUserAssignedIdentity(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->identityName,
        ));

        return UserAssignedIdentityData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(string $location, array $tags = []): UserAssignedIdentityData
    {
        $response = $this->sendArm(new CreateOrUpdateUserAssignedIdentity(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->identityName,
            new UserAssignedIdentityPayload($location, $tags),
        ));

        return UserAssignedIdentityData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteUserAssignedIdentity(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->identityName,
        ));
    }
}
