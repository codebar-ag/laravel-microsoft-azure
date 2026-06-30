<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CreateGroupPayload extends AzurePayload
{
    /**
     * @param  list<string>  $groupTypes
     */
    public function __construct(
        public readonly string $displayName,
        public readonly string $mailNickname,
        public readonly bool $mailEnabled = false,
        public readonly bool $securityEnabled = true,
        public readonly array $groupTypes = ['Unified'],
    ) {}

    public function toAzureBody(): array
    {
        return [
            'displayName' => $this->displayName,
            'mailNickname' => $this->mailNickname,
            'mailEnabled' => $this->mailEnabled,
            'securityEnabled' => $this->securityEnabled,
            'groupTypes' => $this->groupTypes,
        ];
    }
}
