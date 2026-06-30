<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateRoleAssignment extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $scope,
        public readonly string $roleAssignmentName,
        public readonly string $roleDefinitionId,
        public readonly string $principalId,
        public readonly ?string $principalType = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.$this->scope.'/providers/Microsoft.Authorization/roleAssignments/'.$this->roleAssignmentName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_ROLE_ASSIGNMENTS];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        $body = [
            'properties' => [
                'roleDefinitionId' => $this->roleDefinitionId,
                'principalId' => $this->principalId,
            ],
        ];

        if ($this->principalType !== null) {
            $body['properties']['principalType'] = $this->principalType;
        }

        return $body;
    }
}
