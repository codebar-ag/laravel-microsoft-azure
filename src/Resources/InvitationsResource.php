<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Graph\InvitationData;
use CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation;

final class InvitationsResource extends Resource
{
    public function create(
        string $invitedUserEmailAddress,
        string $inviteRedirectUrl,
        bool $sendInvitationMessage = true,
    ): InvitationData {
        $response = $this->sendGraph(new CreateInvitation(
            $invitedUserEmailAddress,
            $inviteRedirectUrl,
            $sendInvitationMessage,
        ));

        return InvitationData::fromAzure($response->json());
    }
}
