<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class ApimSubscriptionPayload extends AzurePayload
{
    public function __construct(
        public readonly string $scope,
        public readonly string $displayName,
        public readonly string $state = 'active',
    ) {}

    public function toAzureBody(): array
    {
        return ['properties' => [
            'scope' => $this->scope,
            'displayName' => $this->displayName,
            'state' => $this->state,
        ]];
    }
}
