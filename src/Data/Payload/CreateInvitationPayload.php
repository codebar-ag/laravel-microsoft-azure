<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CreateInvitationPayload extends AzurePayload
{
    public function __construct(
        public readonly string $invitedUserEmailAddress,
        public readonly string $inviteRedirectUrl,
        public readonly bool $sendInvitationMessage = true,
    ) {}

    public function toAzureBody(): array
    {
        return [
            'invitedUserEmailAddress' => $this->invitedUserEmailAddress,
            'inviteRedirectUrl' => $this->inviteRedirectUrl,
            'sendInvitationMessage' => $this->sendInvitationMessage,
        ];
    }
}
