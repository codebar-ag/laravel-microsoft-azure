<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Invitations;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateInvitation extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $invitedUserEmailAddress,
        public readonly string $inviteRedirectUrl,
        public readonly bool $sendInvitationMessage = true,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/invitations';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'invitedUserEmailAddress' => $this->invitedUserEmailAddress,
            'inviteRedirectUrl' => $this->inviteRedirectUrl,
            'sendInvitationMessage' => $this->sendInvitationMessage,
        ];
    }
}
