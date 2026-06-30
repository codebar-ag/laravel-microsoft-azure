<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

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
        $invitedUser = Arr::get($data, 'invitedUser');

        return new self(
            id: Arr::get($data, 'id'),
            inviteRedeemUrl: Arr::get($data, 'inviteRedeemUrl'),
            invitedUserEmailAddress: Arr::get($data, 'invitedUserEmailAddress'),
            status: Arr::get($data, 'status'),
            invitedUser: is_array($invitedUser) ? UserData::fromAzure($invitedUser) : null,
        );
    }
}
