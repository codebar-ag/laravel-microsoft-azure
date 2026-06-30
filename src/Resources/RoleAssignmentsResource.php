<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\RoleAssignmentData;
use CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment;

final class RoleAssignmentsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $scope,
    ) {
        parent::__construct($client);
    }

    public function create(
        string $roleAssignmentName,
        string $roleDefinitionId,
        string $principalId,
        ?string $principalType = null,
    ): RoleAssignmentData {
        $response = $this->sendArm(new CreateRoleAssignment(
            $this->scope,
            $roleAssignmentName,
            $roleDefinitionId,
            $principalId,
            $principalType,
        ));

        return RoleAssignmentData::fromAzure($response->json());
    }
}
