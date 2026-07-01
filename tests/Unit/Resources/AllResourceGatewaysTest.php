<?php

use CodebarAg\MicrosoftAzure\Data\Arm\CanceledSubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData;
use CodebarAg\MicrosoftAzure\Data\Arm\FunctionData;
use CodebarAg\MicrosoftAzure\Data\Arm\HostKeysData;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Data\Arm\RoleAssignmentData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Arm\WebSiteData;
use CodebarAg\MicrosoftAzure\Data\Graph\ApplicationData;
use CodebarAg\MicrosoftAzure\Data\Graph\GroupData;
use CodebarAg\MicrosoftAzure\Data\Graph\InvitationData;
use CodebarAg\MicrosoftAzure\Data\Graph\PasswordCredentialData;
use CodebarAg\MicrosoftAzure\Data\Graph\ServicePrincipalData;
use CodebarAg\MicrosoftAzure\Data\Graph\UserData;
use CodebarAg\MicrosoftAzure\Data\Kudu\KuduDeploymentData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\ChatCompletionData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\EmbeddingData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\ModelListData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\OpenAiResponseData;
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
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\GetFunction;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\ListFunctions;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListFunctionKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListHostKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListApplicationSettings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\ListSitesByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\RestartSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\SyncFunctionTriggers;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\GetWorkflowStatus;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\AddApplicationPassword;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\CreateApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\DeleteApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\AddGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\CreateGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\DeleteGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroupMembers;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\RemoveGroupMember;
use CodebarAg\MicrosoftAzure\Requests\Graph\Invitations\CreateInvitation;
use CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\CreateServicePrincipal;
use CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\DeleteServicePrincipal;
use CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals\ListServicePrincipals;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\GetUser;
use CodebarAg\MicrosoftAzure\Requests\Graph\Users\ListUsers;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\DeleteSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret;
use CodebarAg\MicrosoftAzure\Requests\Kudu\GetDeploymentStatus;
use CodebarAg\MicrosoftAzure\Requests\Kudu\ZipDeploy;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ChatCompletions;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateResponses;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\Embeddings;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ListModels;
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
        CreateApplication::class => MockResponse::make(body: applicationFixture()),
        AddApplicationPassword::class => MockResponse::make(body: passwordCredentialFixture()),
        DeleteApplication::class => MockResponse::make(status: 204),
        CreateServicePrincipal::class => MockResponse::make(body: servicePrincipalFixture()),
        ListServicePrincipals::class => MockResponse::make(body: ['value' => [servicePrincipalFixture()]]),
        DeleteServicePrincipal::class => MockResponse::make(status: 204),
    ]);

    $graph = new GraphResource($client);

    expect($graph->users()->list())->toHaveCount(1)
        ->and($graph->users()->get('user-1'))->toBeInstanceOf(UserData::class)
        ->and($graph->groups()->create('Readers', 'readers'))->toBeInstanceOf(GroupData::class)
        ->and($graph->groups()->members('group-1')->first())->toBeInstanceOf(UserData::class)
        ->and($graph->invitations()->create('guest@example.test', 'https://redirect.test'))->toBeInstanceOf(InvitationData::class)
        ->and($graph->applications()->create('My App'))->toBeInstanceOf(ApplicationData::class)
        ->and($graph->applications()->addPassword('app-object-1'))->toBeInstanceOf(PasswordCredentialData::class)
        ->and($graph->servicePrincipals()->create('00000000-0000-0000-0000-000000000010'))->toBeInstanceOf(ServicePrincipalData::class)
        ->and($graph->servicePrincipals()->list())->toHaveCount(1)
        ->and($graph->servicePrincipals()->findByAppId('00000000-0000-0000-0000-000000000010'))->toBeInstanceOf(ServicePrincipalData::class)
        ->and($graph->servicePrincipals()->findByAppIdOrFail('00000000-0000-0000-0000-000000000010'))->toBeInstanceOf(ServicePrincipalData::class);

    $graph->groups()->delete('group-1');
    $graph->applications()->delete('app-object-1');
    $graph->servicePrincipals()->delete('sp-object-1');
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

it('covers web, openai, foundry, and function runtime resource gateways', function (): void {
    $webClient = clientWithArmMock([
        ListSitesByResourceGroup::class => MockResponse::make(body: ['value' => [[
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/my-func',
            'name' => 'my-func',
            'location' => 'westeurope',
            'kind' => 'functionapp',
            'properties' => ['defaultHostName' => 'my-func.azurewebsites.net', 'state' => 'Running'],
        ]]]),
        GetSite::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/my-func',
            'name' => 'my-func',
            'location' => 'westeurope',
        ]),
        ListFunctions::class => MockResponse::make(body: ['value' => [[
            'id' => 'func-id',
            'name' => 'HttpTrigger',
            'properties' => ['language' => 'node'],
        ]]]),
        GetFunction::class => MockResponse::make(body: [
            'id' => 'func-id',
            'name' => 'HttpTrigger',
            'properties' => ['language' => 'node'],
        ]),
        ListApplicationSettings::class => MockResponse::make(body: ['properties' => ['KEY' => 'value']]),
        ListHostKeys::class => MockResponse::make(body: ['properties' => ['masterKey' => 'abc']]),
        ListFunctionKeys::class => MockResponse::make(body: ['properties' => ['default' => 'abc']]),
        RestartSite::class => MockResponse::make(status: 200),
        SyncFunctionTriggers::class => MockResponse::make(status: 200),
    ]);

    $app = $webClient->functionApps('sub-1', 'rg-test')->app('my-func');

    expect($webClient->functionApps('sub-1', 'rg-test')->list())->toHaveCount(1)
        ->and($app->get())->toBeInstanceOf(WebSiteData::class)
        ->and($app->listFunctions()->first())->toBeInstanceOf(FunctionData::class)
        ->and($app->functions('HttpTrigger')->get())->toBeInstanceOf(FunctionData::class)
        ->and($app->settings()->list())->toHaveKey('KEY')
        ->and($app->hostKeys()->list())->toBeInstanceOf(HostKeysData::class)
        ->and($app->functions('HttpTrigger')->keys()->list())->toBeInstanceOf(HostKeysData::class);

    $app->restart();
    $app->syncTriggers();

    $openAiClient = clientWithOpenAiMock([
        ChatCompletions::class => MockResponse::make(body: [
            'id' => 'chat-1',
            'model' => 'gpt-4o',
            'choices' => [],
            'usage' => ['prompt_tokens' => 1, 'completion_tokens' => 2, 'total_tokens' => 3],
        ]),
        Embeddings::class => MockResponse::make(body: [
            'model' => 'embed',
            'data' => [['embedding' => [0.1]]],
            'usage' => ['prompt_tokens' => 1, 'total_tokens' => 1],
        ]),
        ListModels::class => MockResponse::make(body: ['data' => [['id' => 'gpt-4o']]]),
        CreateResponses::class => MockResponse::make(body: ['id' => 'resp-1', 'status' => 'completed']),
    ]);

    expect($openAiClient->openAi('my-openai')->chat()->completions('gpt-4o', ['messages' => []]))
        ->toBeInstanceOf(ChatCompletionData::class)
        ->and($openAiClient->openAi('my-openai')->embeddings()->create('embed', ['input' => 'hi']))
        ->toBeInstanceOf(EmbeddingData::class)
        ->and($openAiClient->openAi('my-openai')->models()->list())->toBeInstanceOf(ModelListData::class)
        ->and($openAiClient->openAi('my-openai')->responses()->create(['input' => 'hi']))
        ->toBeInstanceOf(OpenAiResponseData::class);

    $foundryClient = clientWithFoundryMock([
        ListAgents::class => MockResponse::make(body: ['data' => [['id' => 'agent-1']]]),
        GetAgent::class => MockResponse::make(body: ['id' => 'agent-1', 'name' => 'Agent']),
        CreateConversation::class => MockResponse::make(body: ['id' => 'conv-1']),
        CreateProjectResponse::class => MockResponse::make(body: ['id' => 'resp-1']),
    ]);

    expect($foundryClient->foundry('my-foundry', 'default')->agents()->list())->toHaveCount(1)
        ->and($foundryClient->foundry('my-foundry', 'default')->agents()->get('agent-1'))->toHaveKey('id')
        ->and($foundryClient->foundry('my-foundry', 'default')->conversations()->create([]))->toHaveKey('id')
        ->and($foundryClient->foundry('my-foundry', 'default')->responses()->create([]))->toHaveKey('id');

    $runtimeClient = clientWithFunctionRuntimeMock([
        RunWorkflow::class => MockResponse::make(body: ['id' => 'instance-1']),
        GetWorkflowStatus::class => MockResponse::make(body: ['runtimeStatus' => 'Running']),
    ]);

    expect($runtimeClient->functionRuntime('my-func')->workflows()->run('FlowRunner', ['input' => 'test']))
        ->toHaveKey('id')
        ->and($runtimeClient->functionRuntime('my-func')->workflows()->status('FlowRunner', 'run-1'))
        ->toHaveKey('runtimeStatus');
});
it('findByAppIdOrFail throws when service principal is missing', function (): void {
    $client = clientWithGraphMock([
        ListServicePrincipals::class => MockResponse::make(body: ['value' => []]),
    ]);

    expect(fn () => (new GraphResource($client))->servicePrincipals()->findByAppIdOrFail('00000000-0000-0000-0000-000000000099'))
        ->toThrow(RuntimeException::class, 'Service principal for app id [00000000-0000-0000-0000-000000000099] was not found.');
});
