<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Graph\GroupData;
use CodebarAg\MicrosoftAzure\Data\Graph\UserData;
use CodebarAg\MicrosoftAzure\Data\Payload\AddGroupMemberPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateGroupPayload;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\GetGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember;
use Illuminate\Support\Collection;

final class GroupsResource extends Resource
{
    /**
     * @return Collection<int, GroupData>
     */
    public function list(?string $filter = null): Collection
    {
        $response = $this->sendGraph(new ListGroups($filter));

        return $this->mapList($response, 'value', fn (array $item) => GroupData::fromAzure($item));
    }

    public function get(string $groupId): GroupData
    {
        $response = $this->sendGraph(new GetGroup($groupId));

        return GroupData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  list<string>  $groupTypes
     */
    public function create(
        string $displayName,
        string $mailNickname,
        bool $mailEnabled = false,
        bool $securityEnabled = true,
        array $groupTypes = ['Unified'],
    ): GroupData {
        $response = $this->sendGraph(new CreateGroup(new CreateGroupPayload(
            $displayName,
            $mailNickname,
            $mailEnabled,
            $securityEnabled,
            $groupTypes,
        )));

        return GroupData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $groupId): void
    {
        $this->sendGraph(new DeleteGroup($groupId));
    }

    /**
     * @return Collection<int, UserData>
     */
    public function members(string $groupId): Collection
    {
        $response = $this->sendGraph(new ListGroupMembers($groupId));

        return $this->mapList($response, 'value', fn (array $item) => UserData::fromAzure($item));
    }

    public function addMember(string $groupId, string $memberId): void
    {
        $this->sendGraph(new AddGroupMember($groupId, new AddGroupMemberPayload($memberId)));
    }

    public function removeMember(string $groupId, string $memberId): void
    {
        $this->sendGraph(new RemoveGroupMember($groupId, $memberId));
    }
}
