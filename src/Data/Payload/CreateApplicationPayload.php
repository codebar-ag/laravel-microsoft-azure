<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CreateApplicationPayload
{
    public function __construct(
        public readonly string $displayName,
        public readonly string $signInAudience = 'AzureADMyOrg',
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toAzureBody(): array
    {
        return [
            'displayName' => $this->displayName,
            'signInAudience' => $this->signInAudience,
        ];
    }
}
