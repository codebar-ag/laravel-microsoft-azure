<?php

use CodebarAg\MicrosoftAzure\Data\Arm\CanceledSubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentOperationData;
use CodebarAg\MicrosoftAzure\Data\Arm\RoleAssignmentData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Data\Graph\GroupData;
use CodebarAg\MicrosoftAzure\Data\Graph\InvitationData;
use CodebarAg\MicrosoftAzure\Data\Graph\UserData;
use CodebarAg\MicrosoftAzure\Data\KeyVault\SecretIdentifierData;
use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

it('deserializes arm dto payloads', function (): void {
    $role = RoleAssignmentData::fromAzure(roleAssignmentFixture());
    expect($role->principalType)->toBe('ServicePrincipal');

    $operation = DeploymentOperationData::fromAzure(deploymentOperationFixture());
    expect($operation->provisioningState)->toBe(ProvisioningState::Succeeded);

    $sqlRule = SqlFirewallRuleData::fromAzure(sqlFirewallRuleFixture());
    expect($sqlRule->startIpAddress)->toBe('1.2.3.4');

    $sqlDb = SqlDatabaseData::fromAzure(sqlDatabaseFixture());
    expect($sqlDb->name)->toBe('datalogs');

    $deletedVault = DeletedVaultData::fromAzure(deletedVaultFixture());
    expect($deletedVault->name)->toBe('kv-test');

    $deletedAi = DeletedCognitiveServicesAccountData::fromAzure([
        'id' => '/subscriptions/sub-1/providers/Microsoft.CognitiveServices/locations/westeurope/deletedAccounts/aif-test',
        'name' => 'aif-test',
        'properties' => ['location' => 'westeurope'],
    ]);
    expect($deletedAi->name)->toBe('aif-test');

    $canceled = CanceledSubscriptionData::fromAzure(canceledSubscriptionFixture());
    expect($canceled->subscriptionId)->toBe('sub-1');
});

it('deserializes graph dto payloads', function (): void {
    $group = GroupData::fromAzure(groupFixture());
    expect($group->displayName)->toBe('Readers')
        ->and($group->groupTypes)->toBe(['Unified']);

    $user = UserData::fromAzure(userFixture());
    expect($user->mail)->toBe('jane@example.test');

    $invitation = InvitationData::fromAzure(invitationFixture());
    expect($invitation->invitedUser?->id)->toBe('user-1');
});

it('deserializes key vault secret identifier payload', function (): void {
    $identifier = SecretIdentifierData::fromAzure(secretIdentifierFixture());

    expect($identifier->enabled)->toBeTrue()
        ->and($identifier->name)->toBe('abc123');
});

it('deserializes oauth access token payload', function (): void {
    $token = AccessTokenData::fromAzure(accessTokenResponseFixture());

    expect($token->accessToken)->toBe('eyJ.test.token')
        ->and($token->tokenType)->toBe('Bearer')
        ->and($token->expiresIn)->toBe(3600);
});

it('maps deployment mode enum values', function (): void {
    expect(DeploymentMode::Incremental->value)->toBe('Incremental')
        ->and(DeploymentMode::Complete->value)->toBe('Complete');
});

it('maps deployment data with mode enum', function (): void {
    $payload = deploymentFixture();
    $payload['properties']['mode'] = 'Incremental';

    $deployment = DeploymentData::fromAzure($payload);

    expect($deployment->mode)->toBe(DeploymentMode::Incremental);
});
