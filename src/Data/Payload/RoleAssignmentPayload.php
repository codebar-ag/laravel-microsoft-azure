<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class RoleAssignmentPayload extends AzurePayload
{
    public function __construct(
        public readonly string $roleDefinitionId,
        public readonly string $principalId,
        public readonly ?string $principalType = null,
    ) {}

    public function toAzureBody(): array
    {
        $properties = [
            'roleDefinitionId' => $this->roleDefinitionId,
            'principalId' => $this->principalId,
        ];

        if ($this->principalType !== null) {
            $properties['principalType'] = $this->principalType;
        }

        return ['properties' => $properties];
    }
}
