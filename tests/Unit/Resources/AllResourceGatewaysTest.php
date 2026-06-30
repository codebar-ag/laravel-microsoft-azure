<?php

use CodebarAg\MicrosoftAzure\Data\Arm\CanceledSubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Data\Arm\RoleAssignmentData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Graph\GroupData;
use CodebarAg\MicrosoftAzure\Data\Graph\InvitationData;
use CodebarAg\MicrosoftAzure\Data\Graph\UserData;
use CodebarAg\MicrosoftAzure\Data\Kudu\KuduDeploymentData;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\PurgeDeletedVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CancelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups;
use CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\DeleteSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\ListSubscriptionAliases;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\CancelSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret;
use CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus;
use CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy;
use CodebarAg\MicrosoftAzure\Resources\GraphResource;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;

it('covers remaining arm resource gateways', function (): void {
    $client = clientWithArmMock([
        ListResourceGroups::class => MockResponse::make(body: ['value' => [resourceGroupFixture()]]),
        CreateOrUpdateResourceGroup::class => MockResponse::make(body: resourceGroupFixture()),
        CreateRoleAssignment::class => MockResponse::make(body: roleAssignmentFixture()),
        ListDeletedVaults::class => MockResponse::make(body: ['value' => [deletedVaultFixture()]]),
        PurgeDeletedVault::class => MockResponse::make(status: 204),
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => [deletedVaultFixture()]]),
        PurgeDeletedCognitiveServicesAccount::class => MockResponse::make(status: 204),
        CreateOrUpdateSqlFirewallRule::class => MockResponse::make(body: sqlFirewallRuleFixture()),
        DeleteSqlFirewallRule::class => MockResponse::make(status: 204),
        GetSqlDatabase::class => MockResponse::make(body: sqlDatabaseFixture()),
        GetSubscription::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1',
            'subscriptionId' => 'sub-1',
            'displayName' => 'Primary',
            'state' => 'Enabled',
        ]),
        CancelSubscription::class => MockResponse::make(body: canceledSubscriptionFixture()),
        CreateOrUpdateSubscriptionAlias::class => MockResponse::make(body: subscriptionAliasFixture()),
        ListSubscriptionAliases::class => MockResponse::make(body: ['value' => [subscriptionAliasFixture()]]),
        CancelDeployment::class => MockResponse::make(status: 200),
        DeleteResourceGroup::class => MockResponse::make(status: 204),
        DeleteSubscriptionAlias::class => MockResponse::make(status: 204),
        CreateOrUpdateDeployment::class => MockResponse::make(body: deploymentFixture()),
    ]);

    expect($client->resourceGroups('sub-1')->list())->toHaveCount(1)
        ->and($client->resourceGroups('sub-1')->createOrUpdate('rg-test', 'westeurope'))->toBeInstanceOf(ResourceGroupData::class)
        ->and($client->roleAssignments('/subscriptions/sub-1')->create('abc', 'role-id', 'principal-id'))->toBeInstanceOf(RoleAssignmentData::class)
        ->and($client->deletedVaults('sub-1')->list('westeurope')->first())->toBeInstanceOf(DeletedVaultData::class)
        ->and($client->deletedCognitiveServices('sub-1')->list('westeurope')->first())->toBeInstanceOf(DeletedCognitiveServicesAccountData::class)
        ->and($client->sql('sub-1', 'rg-test', 'sql1')->createOrUpdate('rule', '1.1.1.1', '1.1.1.1'))->toBeInstanceOf(SqlFirewallRuleData::class)
        ->and($client->sqlDatabases('sub-1', 'rg-test', 'sql1')->get('datalogs'))->toBeInstanceOf(SqlDatabaseData::class)
        ->and($client->subscriptions()->get('sub-1'))->toBeInstanceOf(SubscriptionData::class)
        ->and($client->subscriptions()->cancel('sub-1'))->toBeInstanceOf(CanceledSubscriptionData::class)
        ->and($client->subscriptionAliases()->createOrUpdate('tenant-acme', '/billing/scope', 'Acme'))->toBeInstanceOf(SubscriptionAliasData::class)
        ->and($client->subscriptionAliases()->list())->toHaveCount(1)
        ->and($client->deployments('sub-1', 'rg-test')->createOrUpdate(
            'tenantflow',
            ['$schema' => 'https://schema.management.azure.com/schemas/2019-04-01/deploymentTemplate.json#'],
        )->name)->toBe('tenantflow');

    $client->resourceGroups('sub-1')->delete('rg-test');
    $client->subscriptionAliases()->delete('tenant-acme');
    $client->deletedVaults('sub-1')->purge('westeurope', 'kv-test');
    $client->deletedCognitiveServices('sub-1')->purge('westeurope', 'cog-test');
    $client->sql('sub-1', 'rg-test', 'sql1')->delete('rule');
    $client->deployments('sub-1', 'rg-test')->cancel('tenantflow');
});

it('covers graph, key vault, and kudu resource gateways', function (): void {
    $client = clientWithGraphMock([
        ListUsers::class => MockResponse::make(body: ['value' => [userFixture()]]),
        GetUser::class => MockResponse::make(body: userFixture()),
        CreateGroup::class => MockResponse::make(body: groupFixture()),
        DeleteGroup::class => MockResponse::make(status: 204),
        ListGroupMembers::class => MockResponse::make(body: ['value' => [userFixture()]]),
        AddGroupMember::class => MockResponse::make(status: 204),
        RemoveGroupMember::class => MockResponse::make(status: 204),
        CreateInvitation::class => MockResponse::make(body: invitationFixture()),
    ]);

    $graph = new GraphResource($client);

    expect($graph->users()->list())->toHaveCount(1)
        ->and($graph->users()->get('user-1'))->toBeInstanceOf(UserData::class)
        ->and($graph->groups()->create('Readers', 'readers'))->toBeInstanceOf(GroupData::class)
        ->and($graph->groups()->members('group-1')->first())->toBeInstanceOf(UserData::class)
        ->and($graph->invitations()->create('guest@example.test', 'https://redirect.test'))->toBeInstanceOf(InvitationData::class);

    $graph->groups()->delete('group-1');
    $graph->groups()->addMember('group-1', 'user-1');
    $graph->groups()->removeMember('group-1', 'user-1');

    $vaultClient = clientWithKeyVaultMock([
        DeleteSecret::class => MockResponse::make(body: secretFixture()),
    ]);

    expect($vaultClient->secrets('myvault')->delete('webhook-token')->name)->toBe('webhook-token');

    $zip = tempnam(sys_get_temp_dir(), 'zip');
    file_put_contents($zip, 'PK'.str_repeat("\0", 20));

    $kuduClient = clientWithKuduMock([
        ZipDeploy::class => MockResponse::make(body: ['id' => 'deploy-1', 'status' => 'Running']),
        GetDeploymentStatus::class => MockResponse::make(body: ['id' => 'deploy-1', 'status' => 'Succeeded']),
    ]);

    expect($kuduClient->appService('my-func')->zipDeploy($zip))->toBeInstanceOf(KuduDeploymentData::class)
        ->and($kuduClient->appService('my-func')->deploymentStatus('deploy-1'))->toBeInstanceOf(KuduDeploymentData::class);

    @unlink($zip);
});

it('resolves vault host and returns nested secrets resource', function (): void {
    $client = clientWithSeededToken();

    expect($client->vault('barevault')->secrets())->not->toBeNull()
        ->and($client->vault('vault.azure.net')->secrets())->not->toBeNull();
});

it('returns empty collections when list payloads are malformed', function (): void {
    $client = clientWithArmMock([
        ListResourceGroups::class => MockResponse::make(body: ['value' => 'not-a-list']),
    ]);

    expect($client->resourceGroups('sub-1')->list())->toHaveCount(0);
});

it('maps list responses when the json key is a top-level array', function (): void {
    $client = clientWithArmMock([
        ListResourceGroups::class => MockResponse::make(body: [resourceGroupFixture()]),
    ]);

    $response = $client->arm()->send(new ListResourceGroups('sub-1'));

    $resource = new class($client) extends CodebarAg\MicrosoftAzure\Resources\Resource
    {
        public function parse(Response $response): Collection
        {
            return $this->mapList($response, null, fn (array $item) => $item['name'] ?? null);
        }
    };

    expect($resource->parse($response)->all())->toBe(['rg-test']);
});

it('maps list responses from associative envelopes without a json key', function (): void {
    $client = clientWithArmMock([
        ListResourceGroups::class => MockResponse::make(body: [
            'value' => [resourceGroupFixture()],
            '@odata.nextLink' => 'https://management.azure.com/next',
        ]),
    ]);

    $response = $client->arm()->send(new ListResourceGroups('sub-1'));

    $resource = new class($client) extends CodebarAg\MicrosoftAzure\Resources\Resource
    {
        public function parse(Response $response): Collection
        {
            return $this->mapList($response, null, fn (array $item) => $item['name'] ?? null);
        }
    };

    expect($resource->parse($response)->all())->toBe(['rg-test']);
});

it('skips non-array items when mapping list responses', function (): void {
    $client = clientWithArmMock([
        ListResourceGroups::class => MockResponse::make(body: ['value' => [resourceGroupFixture(), 'skip-me']]),
    ]);

    expect($client->resourceGroups('sub-1')->list())->toHaveCount(1);
});

it('reads secrets from an fqdn vault host without duplicating the domain', function (): void {
    $client = clientWithKeyVaultMock([
        GetSecret::class => MockResponse::make(body: secretFixture()),
    ], 'myvault.vault.azure.net');

    expect($client->secrets('myvault.vault.azure.net')->get('webhook-token', 'abc123')->name)
        ->toBe('webhook-token');
});
