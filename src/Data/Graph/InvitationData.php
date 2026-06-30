<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class InvitationData extends AzureData
{
    public function __construct(
        public ?string $id = null,
        public ?string $inviteRedeemUrl = null,
        public ?string $invitedUserEmailAddress = null,
        public ?string $status = null,
        public ?UserData $invitedUser = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $invitedUser = $data['invitedUser'] ?? null;

        return new self(
            id: Field::nullableString($data, 'id'),
            inviteRedeemUrl: Field::nullableString($data, 'inviteRedeemUrl'),
            invitedUserEmailAddress: Field::nullableString($data, 'invitedUserEmailAddress'),
            status: Field::nullableString($data, 'status'),
            invitedUser: is_array($invitedUser) ? UserData::fromAzure(Field::stringKeyArray($invitedUser)) : null,
        );
    }
}
