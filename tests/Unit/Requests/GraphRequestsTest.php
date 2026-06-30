<?php

use CodebarAg\MicrosoftAzure\Data\Payload\AddGroupMemberPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateGroupPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateInvitationPayload;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\GetGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers;

it('resolves graph group and user endpoints', function (): void {
    expect((new ListGroups)->resolveEndpoint())->toBe('/groups')
        ->and((new GetGroup('group-1'))->resolveEndpoint())->toBe('/groups/group-1')
        ->and((new DeleteGroup('group-1'))->resolveEndpoint())->toBe('/groups/group-1')
        ->and((new ListGroupMembers('group-1'))->resolveEndpoint())->toBe('/groups/group-1/members')
        ->and((new RemoveGroupMember('group-1', 'user-1'))->resolveEndpoint())->toBe('/groups/group-1/members/user-1/$ref')
        ->and((new GetUser('user-1'))->resolveEndpoint())->toBe('/users/user-1')
        ->and((new CreateInvitation(new CreateInvitationPayload('guest@example.test', 'https://portal.azure.com')))->resolveEndpoint())->toBe('/invitations');
});

it('applies graph list users filter query', function (): void {
    $request = new ListUsers(filter: "mail eq 'jane@example.test'");

    expect($request->query()->all())->toBe(['$filter' => "mail eq 'jane@example.test'"]);
});

it('builds add group member odata reference body', function (): void {
    $request = new AddGroupMember('group-1', new AddGroupMemberPayload('user-1'));

    expect($request->body()->all())
        ->toBe(['@odata.id' => 'https://graph.microsoft.com/v1.0/directoryObjects/user-1']);
});

it('builds create group body with defaults', function (): void {
    $request = new CreateGroup(new CreateGroupPayload(
        displayName: 'Readers',
        mailNickname: 'readers',
    ));

    expect($request->body()->all())
        ->toMatchArray([
            'displayName' => 'Readers',
            'mailNickname' => 'readers',
            'mailEnabled' => false,
            'securityEnabled' => true,
            'groupTypes' => ['Unified'],
        ]);
});

it('builds create invitation body', function (): void {
    $request = new CreateInvitation(new CreateInvitationPayload(
        invitedUserEmailAddress: 'guest@example.test',
        inviteRedirectUrl: 'https://portal.azure.com',
        sendInvitationMessage: false,
    ));

    expect($request->body()->all())
        ->toMatchArray([
            'invitedUserEmailAddress' => 'guest@example.test',
            'inviteRedirectUrl' => 'https://portal.azure.com',
            'sendInvitationMessage' => false,
        ]);
});
