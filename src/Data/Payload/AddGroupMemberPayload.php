<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class AddGroupMemberPayload extends AzurePayload
{
    public function __construct(
        public readonly string $memberId,
    ) {}

    public function toAzureBody(): array
    {
        return [
            '@odata.id' => 'https://graph.microsoft.com/v1.0/directoryObjects/'.$this->memberId,
        ];
    }
}
