<?php

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\HandlesLongRunningOperations;
use CodebarAg\MicrosoftAzure\Data\Arm\ApiKeysData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesModelData;
use CodebarAg\MicrosoftAzure\Data\Arm\CostQueryResultData;
use CodebarAg\MicrosoftAzure\Data\Arm\FoundryProjectData;
use CodebarAg\MicrosoftAzure\Data\Arm\MetricResultData;
use CodebarAg\MicrosoftAzure\Data\Arm\ModelDeploymentData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountKeysData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\ChatCompletionData;
use CodebarAg\MicrosoftAzure\Data\Payload\AppSettingsPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CognitiveServicesAccountPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\ModelDeploymentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateKeyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\ResourceGroupPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\SqlDatabasePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\WebSitePayload;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\CreateOrUpdateCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\DeleteCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\GetCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountModels;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountsByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountSkus;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\RegenerateCognitiveServicesAccountKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\UpdateCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\CreateOrUpdateModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\DeleteModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\GetModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeployments;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeploymentSkus;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\DeleteFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\ListFoundryProjects;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\DeleteVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\DeleteUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\DeleteStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\Support\AbsoluteArmUrl;
use CodebarAg\MicrosoftAzure\Requests\Arm\Support\PollAsyncOperation;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateFunctionKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateHostKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteFunctionKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteHostKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListConnectionStrings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\UpdateApplicationSettings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSiteConfig;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\DeleteSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSiteConfig;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StartSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StopSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\ListSyncFunctionTriggersStatus;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadMessage;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\ListThreadMessages;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\SubmitThreadToolOutputs;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RespondToWorkflow;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateFineTuningJob;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateImageGeneration;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateSpeech;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateTranscription;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\DeleteFile;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ListFiles;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\UploadFile;
use CodebarAg\MicrosoftAzure\Resources\CognitiveServicesResource;
use CodebarAg\MicrosoftAzure\Resources\Resource;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\MicrosoftAzure\Transport\FoundryConnector;
use CodebarAg\MicrosoftAzure\Transport\FunctionRuntimeConnector;
use CodebarAg\MicrosoftAzure\Transport\OpenAiConnector;
use Saloon\Http\Auth\NullAuthenticator;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response as SaloonResponse;

function cognitiveServicesAccountFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test',
        'name' => 'aif-test',
        'location' => 'westeurope',
        'kind' => 'AIServices',
        'sku' => ['name' => 'S0'],
        'properties' => [
            'endpoint' => 'https://aif-test.cognitiveservices.azure.com/',
            'provisioningState' => 'Succeeded',
        ],
        'tags' => ['env' => 'test'],
    ];
}

function modelDeploymentFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/deployments/gpt-4o',
        'name' => 'gpt-4o',
        'sku' => ['name' => 'GlobalStandard', 'capacity' => 1],
        'properties' => [
            'model' => ['format' => 'OpenAI', 'name' => 'gpt-4o', 'version' => '2024-08-06'],
            'provisioningState' => 'Succeeded',
        ],
    ];
}

function foundryProjectFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/projects/proj-1',
        'name' => 'proj-1',
        'location' => 'westeurope',
        'properties' => ['provisioningState' => 'Succeeded'],
    ];
}

function subscriptionAliasBodyWithState(string $state): array
{
    $fixture = subscriptionAliasFixture();
    $fixture['properties']['provisioningState'] = $state;

    return $fixture;
}

function webSiteFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/my-func',
        'name' => 'my-func',
        'location' => 'westeurope',
        'kind' => 'functionapp',
        'properties' => [
            'defaultHostName' => 'my-func.azurewebsites.net',
            'state' => 'Running',
            'provisioningState' => 'Succeeded',
        ],
    ];
}

function fullSurfaceSqlServerFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1',
        'name' => 'sql1',
        'location' => 'westeurope',
        'properties' => [
            'fullyQualifiedDomainName' => 'sql1.database.windows.net',
            'state' => 'Ready',
            'provisioningState' => 'Succeeded',
        ],
    ];
}

/**
 * @return object{awaitAsync: callable, awaitProvisioning: callable}
 */
function lroTestHarness(AzureClient $client): object
{
    return new class($client) extends Resource
    {
        use HandlesLongRunningOperations;

        private int $fakeNow = 1_000_000;

        public function awaitAsync(SaloonResponse $accepted, int $timeoutSeconds = 600, int $defaultIntervalSeconds = 5, ?callable $onTick = null): array
        {
            return $this->awaitAsyncOperation($accepted, $timeoutSeconds, $defaultIntervalSeconds, $onTick);
        }

        public function awaitProvision(callable $fetch, int $timeoutSeconds = 600, int $intervalSeconds = 5, ?callable $onTick = null): object
        {
            return $this->awaitProvisioningState($fetch, $timeoutSeconds, $intervalSeconds, $onTick);
        }

        protected function now(): int
        {
            return $this->fakeNow;
        }

        protected function sleepSeconds(int $seconds): void
        {
            $this->fakeNow += $seconds;
        }
    };
}

it('sets api-key and x-functions-key headers and null auth when keys are provided', function (): void {
    $config = testConnectionConfig();
    $tokens = new EncryptedCacheTokenRepository;
    $fetcher = new ClientCredentialsTokenFetcher;

    $openAi = new OpenAiConnector($config, $tokens, $fetcher, 'my-openai', 'openai-secret');
    $foundry = new FoundryConnector($config, $tokens, $fetcher, 'my-foundry', 'default', 'foundry-secret');
    $runtime = new FunctionRuntimeConnector($config, $tokens, $fetcher, 'my-func', 'host-secret');

    $openAiAuth = (new ReflectionMethod($openAi, 'defaultAuth'))->invoke($openAi);
    $foundryAuth = (new ReflectionMethod($foundry, 'defaultAuth'))->invoke($foundry);
    $runtimeAuth = (new ReflectionMethod($runtime, 'defaultAuth'))->invoke($runtime);

    expect($openAi->defaultHeaders())->toHaveKey('api-key', 'openai-secret')
        ->and($foundry->defaultHeaders())->toHaveKey('api-key', 'foundry-secret')
        ->and($runtime->defaultHeaders())->toHaveKey('x-functions-key', 'host-secret')
        ->and($openAiAuth)->toBeInstanceOf(NullAuthenticator::class)
        ->and($foundryAuth)->toBeInstanceOf(NullAuthenticator::class)
        ->and($runtimeAuth)->toBeInstanceOf(NullAuthenticator::class);

    $openAiOAuth = new OpenAiConnector($config, $tokens, $fetcher, 'my-openai');

    expect($openAiOAuth->defaultHeaders())->not->toHaveKey('api-key');

    clientWithSeededToken();
    $openAiOAuthAuth = (new ReflectionMethod($openAiOAuth, 'defaultAuth'))->invoke($openAiOAuth);

    expect($openAiOAuthAuth)->toBeInstanceOf(TokenAuthenticator::class);
});

it('awaits subscription alias until succeeded', function (): void {
    $client = clientWithArmMock([
        MockResponse::make(body: subscriptionAliasBodyWithState('Running')),
        MockResponse::make(body: subscriptionAliasBodyWithState('Succeeded')),
    ]);

    $alias = $client->subscriptionAliases()->await(
        'tenant-acme',
        timeoutSeconds: 30,
        intervalSeconds: 0,
    );

    expect($alias)->toBeInstanceOf(SubscriptionAliasData::class)
        ->and($alias->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('throws when subscription alias await times out', function (): void {
    $client = clientWithArmMock([
        MockResponse::make(body: subscriptionAliasBodyWithState('Running')),
    ]);

    $harness = lroTestHarness($client);

    expect(fn () => $harness->awaitProvision(
        fn (): SubscriptionAliasData => SubscriptionAliasData::fromAzure(subscriptionAliasBodyWithState('Running')),
        timeoutSeconds: 5,
        intervalSeconds: 5,
    ))->toThrow(LongRunningOperationException::class, 'did not reach a terminal state');
});

it('awaits async operation via Azure-AsyncOperation header until succeeded', function (): void {
    $asyncUrl = 'https://management.azure.com/subscriptions/sub-1/operations/op-1?api-version=2020-06-01';

    $client = clientWithArmMock([
        GetSubscriptionAlias::class => MockResponse::make(
            body: ['name' => 'pending'],
            status: 202,
            headers: ['Azure-AsyncOperation' => $asyncUrl],
        ),
        MockResponse::make(body: ['status' => 'Running'], headers: ['Retry-After' => ['3', '5']]),
        MockResponse::make(body: ['status' => 'Succeeded', 'id' => 'op-1']),
    ]);

    $accepted = $client->arm()->send(new GetSubscriptionAlias('tenant-acme'));
    $harness = lroTestHarness($client);
    $tickCount = 0;
    $result = $harness->awaitAsync(
        $accepted,
        timeoutSeconds: 30,
        defaultIntervalSeconds: 0,
        onTick: function () use (&$tickCount): void {
            $tickCount++;
        },
    );

    expect($result)->toMatchArray(['status' => 'Succeeded', 'id' => 'op-1'])
        ->and($tickCount)->toBeGreaterThan(0);
});

it('returns sync body when async operation has no tracking header', function (): void {
    $client = clientWithArmMock([
        GetSubscriptionAlias::class => MockResponse::make(
            body: ['status' => 'Succeeded', 'sync' => true],
            status: 200,
        ),
    ]);

    $accepted = $client->arm()->send(new GetSubscriptionAlias('tenant-acme'));
    $harness = lroTestHarness($client);
    $result = $harness->awaitAsync($accepted);

    expect($result)->toMatchArray(['status' => 'Succeeded', 'sync' => true]);
});

it('throws when async operation finishes in failed state', function (): void {
    $asyncUrl = 'https://management.azure.com/subscriptions/sub-1/operations/op-fail?api-version=2020-06-01';

    $client = clientWithArmMock([
        GetSubscriptionAlias::class => MockResponse::make(
            body: [],
            status: 202,
            headers: ['Azure-AsyncOperation' => $asyncUrl],
        ),
        PollAsyncOperation::class => MockResponse::make(body: ['status' => 'Failed']),
    ]);

    $accepted = $client->arm()->send(new GetSubscriptionAlias('tenant-acme'));
    $harness = lroTestHarness($client);

    expect(fn () => $harness->awaitAsync($accepted, timeoutSeconds: 30, defaultIntervalSeconds: 0))
        ->toThrow(LongRunningOperationException::class, 'non-success state [Failed]');
});

it('resolves absolute arm urls and poll async operation endpoints', function (): void {
    $absolute = 'https://management.azure.com/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Resources/deployments/d1/operations?api-version=2021-04-01&$skiptoken=abc';

    expect(AbsoluteArmUrl::toEndpoint($absolute))
        ->toBe('/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Resources/deployments/d1/operations?api-version=2021-04-01&$skiptoken=abc')
        ->and(AbsoluteArmUrl::toEndpoint('/relative/path?api-version=1'))
        ->toBe('/relative/path?api-version=1');

    $poll = new PollAsyncOperation($absolute);

    expect($poll->resolveEndpoint())
        ->toBe('/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Resources/deployments/d1/operations?api-version=2021-04-01&$skiptoken=abc');
});

it('covers cognitive services resource gateways end to end', function (): void {
    $client = clientWithArmMock([
        ListCognitiveServicesAccountsByResourceGroup::class => MockResponse::make(body: ['value' => [cognitiveServicesAccountFixture()]]),
        ListCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => [cognitiveServicesAccountFixture()]]),
        GetCognitiveServicesAccount::class => MockResponse::make(body: cognitiveServicesAccountFixture()),
        CreateOrUpdateCognitiveServicesAccount::class => MockResponse::make(body: cognitiveServicesAccountFixture()),
        UpdateCognitiveServicesAccount::class => MockResponse::make(body: cognitiveServicesAccountFixture()),
        DeleteCognitiveServicesAccount::class => MockResponse::make(status: 204),
        ListCognitiveServicesAccountKeys::class => MockResponse::make(body: ['key1' => 'secret-1', 'key2' => 'secret-2']),
        RegenerateCognitiveServicesAccountKey::class => MockResponse::make(body: ['key1' => 'regenerated']),
        ListCognitiveServicesAccountModels::class => MockResponse::make(body: ['value' => [['name' => 'gpt-4o', 'version' => '1', 'format' => 'OpenAI']]]),
        ListCognitiveServicesAccountSkus::class => MockResponse::make(body: ['value' => [['name' => 'S0']]]),
        ListModelDeployments::class => MockResponse::make(body: ['value' => [modelDeploymentFixture()]]),
        GetModelDeployment::class => MockResponse::make(body: modelDeploymentFixture()),
        CreateOrUpdateModelDeployment::class => MockResponse::make(body: modelDeploymentFixture()),
        DeleteModelDeployment::class => MockResponse::make(status: 204),
        ListModelDeploymentSkus::class => MockResponse::make(body: ['value' => [['name' => 'GlobalStandard']]]),
        ListFoundryProjects::class => MockResponse::make(body: ['value' => [foundryProjectFixture()]]),
        GetFoundryProject::class => MockResponse::make(body: foundryProjectFixture()),
        CreateOrUpdateFoundryProject::class => MockResponse::make(body: foundryProjectFixture()),
        DeleteFoundryProject::class => MockResponse::make(status: 204),
    ]);

    $cog = $client->cognitiveServices('sub-1', 'rg-test');

    expect($cog)->toBeInstanceOf(CognitiveServicesResource::class)
        ->and($cog->list())->toHaveCount(1)
        ->and($cog->listAllInSubscription())->toHaveCount(1);

    $account = $cog->account('aif-test');

    expect($account->get())->toBeInstanceOf(CognitiveServicesAccountData::class)
        ->and($account->createOrUpdate('westeurope'))->toBeInstanceOf(CognitiveServicesAccountData::class)
        ->and($account->update('westeurope'))->toBeInstanceOf(CognitiveServicesAccountData::class)
        ->and($account->listKeys())->toBeInstanceOf(ApiKeysData::class)
        ->and($account->regenerateKey('key1')->key1)->toBe('regenerated')
        ->and($account->listModels()->first())->toBeInstanceOf(CognitiveServicesModelData::class)
        ->and($account->listSkus()->first())->toHaveKey('name', 'S0')
        ->and($account->projects()->list()->first())->toBeInstanceOf(FoundryProjectData::class)
        ->and($account->projects()->get('proj-1')->name)->toBe('proj-1')
        ->and($account->projects()->createOrUpdate('proj-1', 'westeurope')->name)->toBe('proj-1')
        ->and($account->deployments()->list()->first())->toBeInstanceOf(ModelDeploymentData::class)
        ->and($account->deployments()->get('gpt-4o')->name)->toBe('gpt-4o')
        ->and($account->deployments()->createOrUpdate('gpt-4o', 'OpenAI', 'gpt-4o', '2024-08-06')->name)->toBe('gpt-4o')
        ->and($account->deployments()->listSkus('gpt-4o')->first())->toHaveKey('name', 'GlobalStandard');

    expect($cog->createOrUpdate('aif-test', 'westeurope')->name)->toBe('aif-test');

    $account->delete();
    $account->projects()->delete('proj-1');
    $account->deployments()->delete('gpt-4o');
});

it('covers foundry agents conversations and threads resource gateways', function (): void {
    $client = clientWithFoundryMock([
        CreateAgent::class => MockResponse::make(body: ['id' => 'agent-1', 'name' => 'Agent']),
        DeleteAgent::class => MockResponse::make(status: 204),
        CreateAgentVersion::class => MockResponse::make(body: ['id' => 'agent-1', 'version' => '2']),
        GetAgentVersion::class => MockResponse::make(body: ['id' => 'agent-1', 'version' => '1']),
        GetConversation::class => MockResponse::make(body: ['id' => 'conv-1']),
        CreateConversationItems::class => MockResponse::make(body: ['id' => 'item-1']),
        CreateThread::class => MockResponse::make(body: ['id' => 'thread-1']),
        GetThread::class => MockResponse::make(body: ['id' => 'thread-1']),
        CreateThreadMessage::class => MockResponse::make(body: ['id' => 'msg-1']),
        ListThreadMessages::class => MockResponse::make(body: ['data' => [['id' => 'msg-1']]]),
        CreateThreadRun::class => MockResponse::make(body: ['id' => 'run-1']),
        GetThreadRun::class => MockResponse::make(body: ['id' => 'run-1', 'status' => 'completed']),
        SubmitThreadToolOutputs::class => MockResponse::make(body: ['id' => 'run-1', 'status' => 'completed']),
    ]);

    $foundry = $client->foundry('my-foundry', 'default');

    expect($foundry->agents()->create(['name' => 'Agent']))->toHaveKey('id', 'agent-1')
        ->and($foundry->agents()->createVersion('agent-1', []))->toHaveKey('version', '2')
        ->and($foundry->agents()->getVersion('agent-1', '1'))->toHaveKey('version', '1')
        ->and($foundry->conversations()->get('conv-1'))->toHaveKey('id', 'conv-1')
        ->and($foundry->conversations()->createItems('conv-1', ['items' => []]))->toHaveKey('id', 'item-1')
        ->and($foundry->threads()->create([]))->toHaveKey('id', 'thread-1')
        ->and($foundry->threads()->get('thread-1'))->toHaveKey('id', 'thread-1')
        ->and($foundry->threads()->createMessage('thread-1', []))->toHaveKey('id', 'msg-1')
        ->and($foundry->threads()->listMessages('thread-1'))->toHaveCount(1)
        ->and($foundry->threads()->createRun('thread-1', []))->toHaveKey('id', 'run-1')
        ->and($foundry->threads()->getRun('thread-1', 'run-1'))->toHaveKey('status', 'completed')
        ->and($foundry->threads()->submitToolOutputs('thread-1', 'run-1', []))->toHaveKey('status', 'completed');

    $foundry->agents()->delete('agent-1');
});

it('covers openai audio images files and fine tuning resource gateways', function (): void {
    $tempFile = tempnam(sys_get_temp_dir(), 'openai-upload');
    file_put_contents($tempFile, 'file-content');

    $client = clientWithOpenAiMock([
        CreateSpeech::class => MockResponse::make(body: ['audio' => 'base64-audio']),
        CreateTranscription::class => MockResponse::make(body: ['text' => 'hello world']),
        CreateImageGeneration::class => MockResponse::make(body: ['data' => [['url' => 'https://example.test/image.png']]]),
        ListFiles::class => MockResponse::make(body: ['data' => [['id' => 'file-1', 'purpose' => 'fine-tune']]]),
        UploadFile::class => MockResponse::make(body: ['id' => 'file-2', 'purpose' => 'fine-tune']),
        DeleteFile::class => MockResponse::make(body: ['id' => 'file-1', 'deleted' => true]),
        CreateFineTuningJob::class => MockResponse::make(body: ['id' => 'ftjob-1', 'status' => 'queued']),
    ]);

    $openAi = $client->openAi('my-openai');

    expect($openAi->audio()->speech('tts', ['input' => 'hi']))->toHaveKey('audio')
        ->and($openAi->audio()->transcription('whisper', []))->toHaveKey('text')
        ->and($openAi->images()->generate('dalle', ['prompt' => 'cat']))->toHaveKey('data')
        ->and($openAi->files()->list())->toHaveKey('data')
        ->and($openAi->files()->upload($tempFile, 'fine-tune'))->toHaveKey('id', 'file-2')
        ->and($openAi->files()->delete('file-1'))->toHaveKey('deleted', true)
        ->and($openAi->fineTuning()->createJob(['model' => 'gpt-4o']))->toHaveKey('status', 'queued');

    @unlink($tempFile);
});

it('covers function app lifecycle settings keys and workflow respond', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateSite::class => MockResponse::make(body: webSiteFixture()),
        DeleteSite::class => MockResponse::make(status: 204),
        StartSite::class => MockResponse::make(status: 200),
        StopSite::class => MockResponse::make(status: 200),
        GetSiteConfig::class => MockResponse::make(body: ['properties' => ['numberOfWorkers' => 1]]),
        CreateOrUpdateSiteConfig::class => MockResponse::make(body: ['properties' => ['numberOfWorkers' => 2]]),
        UpdateApplicationSettings::class => MockResponse::make(body: ['properties' => ['KEY' => 'updated']]),
        ListConnectionStrings::class => MockResponse::make(body: ['properties' => ['DefaultConnection' => ['value' => 'conn']]]),
        CreateOrUpdateHostKey::class => MockResponse::make(body: ['properties' => ['masterKey' => 'new-master']]),
        DeleteHostKey::class => MockResponse::make(status: 204),
        CreateOrUpdateFunctionKey::class => MockResponse::make(body: ['properties' => ['default' => 'new-key']]),
        DeleteFunctionKey::class => MockResponse::make(status: 204),
        ListSyncFunctionTriggersStatus::class => MockResponse::make(body: ['status' => 'success']),
    ]);

    $app = $client->functionApps('sub-1', 'rg-test')->app('my-func');

    expect($app->createOrUpdate('westeurope')->name)->toBe('my-func')
        ->and($app->getConfig())->toHaveKey('properties.numberOfWorkers', 1)
        ->and($app->createOrUpdateConfig(['numberOfWorkers' => 2]))->toHaveKey('properties.numberOfWorkers', 2)
        ->and($app->settings()->update(['KEY' => 'updated']))->toHaveKey('KEY', 'updated')
        ->and($app->settings()->listConnectionStrings())->toHaveKey('properties')
        ->and($app->hostKeys()->createOrUpdate('default', 'secret')->properties)->toHaveKey('masterKey')
        ->and($app->functions('HttpTrigger')->keys()->createOrUpdate('default', 'secret')->properties)->toHaveKey('default')
        ->and($app->syncTriggersStatus())->toHaveKey('status', 'success');

    $app->start();
    $app->stop();
    $app->delete();
    $app->hostKeys()->delete('default');
    $app->functions('HttpTrigger')->keys()->delete('default');

    $runtimeClient = clientWithFunctionRuntimeMock([
        RespondToWorkflow::class => MockResponse::make(body: ['runtimeStatus' => 'Completed']),
    ]);

    expect($runtimeClient->functionRuntime('my-func')->workflows()->respond('FlowRunner', 'run-1', ['input' => true]))
        ->toHaveKey('runtimeStatus', 'Completed');
});

it('covers sql server databases firewall storage vault and identity deletes', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateSqlServer::class => MockResponse::make(body: fullSurfaceSqlServerFixture()),
        CreateOrUpdateSqlFirewallRule::class => MockResponse::make(body: sqlFirewallRuleFixture()),
        GetSqlDatabase::class => MockResponse::make(body: sqlDatabaseFixture()),
        DeleteStorageAccount::class => MockResponse::make(status: 204),
        DeleteVault::class => MockResponse::make(status: 204),
        DeleteUserAssignedIdentity::class => MockResponse::make(status: 204),
    ]);

    $server = $client->sqlServers('sub-1', 'rg-test')->server('sql1');

    expect($server->createOrUpdate('westeurope', 'sqladmin'))->not->toBeNull()
        ->and($server->firewallRules()->createOrUpdate('rule', '1.1.1.1', '1.1.1.1'))->toBeInstanceOf(SqlFirewallRuleData::class)
        ->and($server->databases()->get('datalogs'))->toBeInstanceOf(SqlDatabaseData::class);

    $client->storageAccounts('sub-1', 'rg-test')->account('sa1')->delete();
    $client->vaults('sub-1', 'rg-test')->vault('kv1')->delete();
    $client->managedIdentities('sub-1', 'rg-test')->identity('id1')->delete();
});

it('builds request bodies and deserializes dto edge cases', function (): void {
    expect((new CreateOrUpdateCognitiveServicesAccount(
        'sub-1', 'rg-test', 'aif-test',
        new CognitiveServicesAccountPayload('westeurope'),
    ))->body()->all())->toHaveKey('location', 'westeurope')
        ->and((new UpdateCognitiveServicesAccount(
            'sub-1', 'rg-test', 'aif-test',
            new CognitiveServicesAccountPayload('westeurope', tags: ['a' => 'b']),
        ))->body()->all())->toHaveKey('tags.a', 'b')
        ->and((new RegenerateCognitiveServicesAccountKey(
            'sub-1', 'rg-test', 'aif-test',
            new RegenerateKeyPayload('key2'),
        ))->body()->all())->toBe(['keyName' => 'key2'])
        ->and((new CreateOrUpdateModelDeployment(
            'sub-1', 'rg-test', 'aif-test', 'gpt-4o',
            new ModelDeploymentPayload('OpenAI', 'gpt-4o'),
        ))->body()->all())->toHaveKey('properties.model.name', 'gpt-4o')
        ->and((new CreateOrUpdateFoundryProject(
            'sub-1', 'rg-test', 'aif-test', 'proj-1',
            new FoundryProjectPayload('westeurope', ['displayName' => 'P1']),
        ))->body()->all())->toHaveKey('properties.displayName', 'P1')
        ->and((new CreateOrUpdateSite(
            'sub-1', 'rg-test', 'my-func',
            new WebSitePayload('westeurope'),
        ))->body()->all())->toHaveKey('location', 'westeurope')
        ->and((new UpdateApplicationSettings(
            'sub-1', 'rg-test', 'my-func',
            new AppSettingsPayload(['A' => 'B']),
        ))->body()->all())->toBe(['properties' => ['A' => 'B']])
        ->and((new CreateOrUpdateHostKey(
            'sub-1', 'rg-test', 'my-func', 'default',
            new FunctionKeyPayload('secret'),
        ))->body()->all())->toBe(['properties' => ['value' => 'secret']])
        ->and((new CreateOrUpdateFunctionKey(
            'sub-1', 'rg-test', 'my-func', 'HttpTrigger', 'default',
            new FunctionKeyPayload('secret'),
        ))->body()->all())->toBe(['properties' => ['value' => 'secret']])
        ->and((new CreateOrUpdateSiteConfig(
            'sub-1', 'rg-test', 'my-func',
            new GenericJsonPayload(['alwaysOn' => true]),
        ))->body()->all())->toBe(['alwaysOn' => true])
        ->and((new RespondToWorkflow('FlowRunner', 'run-1', new GenericJsonPayload(['input' => true])))->body()->all())
        ->toBe(['input' => true])
        ->and((new CreateAgent(new GenericJsonPayload(['name' => 'agent-1'])))->body()->all())
        ->toBe(['name' => 'agent-1']);

    $payload = new ResourceGroupPayload('westeurope', ['provisioningState' => 'Succeeded'], ['env' => 'test']);

    expect($payload->toAzureBody())
        ->toMatchArray([
            'location' => 'westeurope',
            'properties' => ['provisioningState' => 'Succeeded'],
            'tags' => ['env' => 'test'],
        ]);

    expect(TokenAudience::CognitiveServicesDataPlane->scope())
        ->toBe('https://cognitiveservices.azure.com/.default')
        ->and(ApiKeysData::fromAzure(['key1' => 'k1', 'key2' => 'k2'])->key1)->toBe('k1')
        ->and(CognitiveServicesAccountData::fromAzure(cognitiveServicesAccountFixture())->endpoint)
        ->toBe('https://aif-test.cognitiveservices.azure.com/')
        ->and(CognitiveServicesModelData::fromAzure(['name' => 'gpt-4o', 'version' => '1', 'format' => 'OpenAI'])->format)
        ->toBe('OpenAI')
        ->and(FoundryProjectData::fromAzure(foundryProjectFixture())->provisioningState)
        ->toBe(ProvisioningState::Succeeded)
        ->and(ModelDeploymentData::fromAzure(modelDeploymentFixture())->modelName)->toBe('gpt-4o')
        ->and(ChatCompletionData::fromAzure([
            'id' => 'chat-1',
            'model' => 'gpt-4o',
            'choices' => [['message' => ['role' => 'assistant', 'content' => 'Hi']]],
            'usage' => ['prompt_tokens' => 1, 'completion_tokens' => 2, 'total_tokens' => 3],
        ])->usage?->totalTokens)->toBe(3)
        ->and(CostQueryResultData::fromAzure([
            'properties' => [
                'columns' => [['name' => 'BillingCurrency']],
                'rows' => [['USD']],
            ],
        ])->currency)->toBe('USD')
        ->and(MetricResultData::fromAzure([
            'name' => ['value' => 'CpuTime'],
            'unit' => 'Seconds',
            'timeseries' => [['data' => [['timestamp' => '2026-01-01T00:00:00Z', 'average' => 3.5]]]],
        ])->points[0]['total'])->toBe(3.5)
        ->and(StorageAccountKeysData::fromAzure([
            'keys' => [
                ['keyName' => 'key1', 'value' => 'secret-1'],
                'skip-me',
            ],
        ])->keys)->toHaveCount(1);

    $tempFile = tempnam(sys_get_temp_dir(), 'upload');
    file_put_contents($tempFile, 'content');

    expect((new UploadFile($tempFile, 'fine-tune'))->body()->all())->toHaveCount(2);

    @unlink($tempFile);
});

it('covers remaining long running operation and payload edge cases', function (): void {
    $asyncUrl = 'https://management.azure.com/subscriptions/sub-1/operations/op-slow?api-version=2020-06-01';

    $client = clientWithArmMock([
        GetSubscriptionAlias::class => MockResponse::make(
            body: ['name' => 'pending'],
            status: 202,
            headers: ['Location' => $asyncUrl],
        ),
        MockResponse::make(body: ['status' => 'Running'], headers: ['Retry-After' => ['5', '10']]),
        MockResponse::make(body: ['status' => 'Running']),
    ]);

    $accepted = $client->arm()->send(new GetSubscriptionAlias('tenant-acme'));
    $harness = lroTestHarness($client);

    expect(fn () => $harness->awaitAsync(
        $accepted,
        timeoutSeconds: 5,
        defaultIntervalSeconds: 5,
    ))->toThrow(LongRunningOperationException::class, 'did not complete within');

    expect((new WebSitePayload('westeurope', tags: ['env' => 'test']))->toAzureBody())
        ->toHaveKey('tags.env', 'test')
        ->and((new SqlDatabasePayload('westeurope', tags: ['db' => 'logs']))->toAzureBody())
        ->toHaveKey('tags.db', 'logs')
        ->and(CostQueryResultData::fromAzure([
            'properties' => [
                'columns' => [['name' => 'Cost']],
                'rows' => ['skip-me', [1.0]],
            ],
        ])->rows)->toHaveCount(1)
        ->and(MetricResultData::fromAzure([
            'name' => ['value' => 'Requests'],
            'unit' => 'Count',
            'timeseries' => 'invalid',
        ])->points)->toBe([])
        ->and(MetricResultData::fromAzure([
            'name' => ['value' => 'Requests'],
            'unit' => 'Count',
            'timeseries' => [['data' => ['skip-me', ['timestamp' => '2026-01-01T00:00:00Z', 'total' => 1]]]],
        ])->points)->toHaveCount(1);

    expect(fn () => (new UploadFile('/path/that/does/not/exist', 'fine-tune'))->body()->all())
        ->toThrow(RuntimeException::class, 'is not readable');
});

it('invokes real sleep when await interval is greater than zero', function (): void {
    $client = clientWithArmMock([
        MockResponse::make(body: subscriptionAliasBodyWithState('Running')),
        MockResponse::make(body: subscriptionAliasBodyWithState('Succeeded')),
    ]);

    $start = microtime(true);

    $client->subscriptionAliases()->await('tenant-acme', timeoutSeconds: 30, intervalSeconds: 1);

    expect(microtime(true) - $start)->toBeGreaterThan(0.5);
});
