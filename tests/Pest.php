<?php

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\ListSubscriptions;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveSubscriptionAliasTestContext;
use CodebarAg\MicrosoftAzure\Tests\Support\MicrosoftAzureFixture;
use CodebarAg\MicrosoftAzure\Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Request;
use Saloon\Http\Response;

uses(TestCase::class)->in(__DIR__);

uses()
    ->beforeEach(function (): void {
        Cache::flush();
        app(MicrosoftAzureManager::class)->forget();
    })
    ->afterEach(function (): void {
        MockClient::destroyGlobal();
    })
    ->in('Unit', 'Core');

uses()
    ->group('integration')
    ->beforeEach(function (): void {
        if (! integrationCredentialsConfigured()) {
            test()->markTestSkipped('MICROSOFT_AZURE_* integration credentials are not configured.');
        }

        configureIntegrationConnection();
        configureIntegrationRecording();
    })
    ->in('Integration');

function runAzureIntegration(callable $callback): void
{
    try {
        $callback();
    } catch (ForbiddenException $e) {
        $hint = str_contains($e->getMessage(), 'invoice section')
            || str_contains($e->getMessage(), 'billing')
            ? 'Grant subscription-creator (or equivalent) permissions on MICROSOFT_AZURE_TESTS_BILLING_SCOPE.'
            : 'Assign Contributor (or equivalent) on MICROSOFT_AZURE_SUBSCRIPTION_ID.';

        test()->markTestSkipped("Azure RBAC: {$hint} — {$e->getMessage()}");
    }
}

function integrationFixturesEnabled(): bool
{
    return filter_var(env('MICROSOFT_AZURE_RECORD_FIXTURES', false), FILTER_VALIDATE_BOOLEAN);
}

function configureIntegrationRecording(): void
{
    if (! integrationFixturesEnabled()) {
        return;
    }

    Azure::instance()->arm()->withMockClient(new MockClient([
        ListSubscriptions::class => new MicrosoftAzureFixture('list-subscriptions'),
        GetSubscription::class => new MicrosoftAzureFixture('get-subscription'),
        CreateOrUpdateResourceGroup::class => new MicrosoftAzureFixture('create-resource-group'),
        GetResourceGroup::class => new MicrosoftAzureFixture('get-resource-group'),
        ListResourceGroups::class => new MicrosoftAzureFixture('list-resource-groups'),
        DeleteResourceGroup::class => new MicrosoftAzureFixture('delete-resource-group'),
    ]));
}

function integrationCredentialsConfigured(): bool
{
    return filled(env('MICROSOFT_AZURE_TENANT_ID'))
        && filled(env('MICROSOFT_AZURE_CLIENT_ID'))
        && filled(env('MICROSOFT_AZURE_CLIENT_SECRET'))
        && filled(env('MICROSOFT_AZURE_SUBSCRIPTION_ID'));
}

function configureIntegrationConnection(): void
{
    config()->set('laravel-microsoft-azure.connections.default', [
        'tenant_id' => env('MICROSOFT_AZURE_TENANT_ID'),
        'client_id' => env('MICROSOFT_AZURE_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_AZURE_CLIENT_SECRET'),
        'subscription_id' => env('MICROSOFT_AZURE_SUBSCRIPTION_ID'),
        'cache_driver' => 'array',
        'cache_lifetime_in_seconds' => (int) env('MICROSOFT_AZURE_CACHE_LIFETIME_IN_SECONDS', 0),
        'request_timeout_in_seconds' => (int) env('MICROSOFT_AZURE_REQUEST_TIMEOUT_IN_SECONDS', 60),
    ]);

    app(MicrosoftAzureManager::class)->forget();
}

function integrationSubscriptionId(): string
{
    return (string) env('MICROSOFT_AZURE_SUBSCRIPTION_ID');
}

function integrationLocation(): string
{
    return (string) env('MICROSOFT_AZURE_TESTS_LOCATION', 'westeurope');
}

function integrationBillingScopeConfigured(): bool
{
    return filled(env('MICROSOFT_AZURE_TESTS_BILLING_SCOPE'));
}

function integrationBillingScope(): string
{
    return (string) env('MICROSOFT_AZURE_TESTS_BILLING_SCOPE');
}

function skipUnlessBillingScopeConfigured(): void
{
    if (! integrationBillingScopeConfigured()) {
        test()->markTestSkipped('MICROSOFT_AZURE_TESTS_BILLING_SCOPE is not configured.');
    }
}

function pollSubscriptionAlias(string $aliasName, int $timeoutSeconds = 300): SubscriptionAliasData
{
    $deadline = time() + $timeoutSeconds;

    do {
        $alias = Azure::instance()->subscriptionAliases()->get($aliasName);

        if ($alias->provisioningState?->isTerminal()) {
            return $alias;
        }

        sleep(5);
    } while (time() < $deadline);

    throw new RuntimeException(
        "Subscription alias [{$aliasName}] did not reach a terminal provisioning state within {$timeoutSeconds} seconds."
    );
}

/**
 * @param  callable(LiveSubscriptionAliasTestContext): void  $callback
 */
function withLiveSubscriptionAlias(callable $callback): void
{
    skipUnlessBillingScopeConfigured();

    runAzureIntegration(function () use ($callback): void {
        $context = LiveSubscriptionAliasTestContext::provision();

        try {
            $callback($context);
        } finally {
            $context->teardown();
        }
    });
}

/**
 * @param  callable(LiveAzureTestContext): void  $callback
 */
function withLiveResourceGroup(callable $callback): void
{
    runAzureIntegration(function () use ($callback): void {
        $context = LiveAzureTestContext::provisionResourceGroup();

        try {
            $callback($context);
        } finally {
            $context->teardown();
        }
    });
}

function testConnectionConfig(): ConnectionConfig
{
    return ConnectionConfig::make('test', [
        'tenantId' => '00000000-0000-0000-0000-000000000001',
        'clientId' => '00000000-0000-0000-0000-000000000002',
        'clientSecret' => 'test-secret',
        'subscriptionId' => '00000000-0000-0000-0000-000000000003',
        'cacheDriver' => 'array',
        'cacheLifetimeInSeconds' => 3600,
        'requestTimeoutInSeconds' => 30,
    ]);
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithMock(array $responses): AzureClient
{
    return clientWithArmMock($responses);
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithArmMock(array $responses): AzureClient
{
    $client = clientWithSeededToken();

    $client->arm()->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithGraphMock(array $responses): AzureClient
{
    $client = clientWithSeededToken();

    $client->graph()->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithKeyVaultMock(array $responses, string $vaultName = 'myvault'): AzureClient
{
    $client = clientWithSeededToken();

    $client->keyVault(str_contains($vaultName, '.') ? $vaultName : $vaultName.'.vault.azure.net')
        ->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithKuduMock(array $responses, string $appName = 'my-func'): AzureClient
{
    $client = clientWithSeededToken();

    $client->kudu($appName)->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithOpenAiMock(array $responses, string $accountName = 'my-openai', ?string $apiKey = null): AzureClient
{
    $client = clientWithSeededToken();

    $client->openAiConnector($accountName, $apiKey)->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithFoundryMock(
    array $responses,
    string $accountName = 'my-foundry',
    string $projectName = 'default',
    ?string $apiKey = null,
): AzureClient {
    $client = clientWithSeededToken();

    $client->foundryConnector($accountName, $projectName, $apiKey)->withMockClient(new MockClient($responses));

    return $client;
}

/**
 * @param  array<int, MockResponse>|array<class-string, MockResponse>  $responses
 */
function clientWithFunctionRuntimeMock(array $responses, string $appName = 'my-func', ?string $hostKey = null): AzureClient
{
    $client = clientWithSeededToken(functionRuntimeAppName: $appName);

    $client->functionRuntimeConnector($appName, $hostKey)->withMockClient(new MockClient($responses));

    return $client;
}

function clientWithSeededToken(string $kuduAppName = 'my-func', string $functionRuntimeAppName = 'my-func'): AzureClient
{
    $manager = app(MicrosoftAzureManager::class);
    $config = testConnectionConfig();

    $token = new AccessTokenData(
        accessToken: 'seeded-access-token',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    );

    $audiences = [
        TokenAudience::Arm->value => null,
        TokenAudience::Graph->value => null,
        TokenAudience::KeyVault->value => null,
        TokenAudience::Kudu->value => $kuduAppName.'.scm.azurewebsites.net',
        TokenAudience::CognitiveServicesDataPlane->value => null,
        TokenAudience::FunctionRuntime->value => $functionRuntimeAppName.'.azurewebsites.net',
    ];

    foreach ($audiences as $audience => $scopeHost) {
        $suffix = $scopeHost !== null ? '.'.hash('sha256', $scopeHost) : '';
        Cache::store($config->cacheDriver)
            ->put(
                'microsoft-azure.oauth.'.$config->identifier().'.'.$audience.$suffix,
                Crypt::encrypt($token),
                3600,
            );
    }

    return $manager->connection($config);
}

function recordFixture(Request $request, string $name): Response
{
    $path = __DIR__.'/Fixtures/saloon/'.$name.'.json';

    if (filter_var(env('MICROSOFT_AZURE_RECORD_FIXTURES', false), FILTER_VALIDATE_BOOLEAN) && file_exists($path)) {
        unlink($path);
    }

    $connector = Azure::instance()->arm();
    $connector->withMockClient(new MockClient([
        $request::class => new MicrosoftAzureFixture($name),
    ]));

    return $connector->send($request);
}

function resourceGroupFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test',
        'name' => 'rg-test',
        'location' => 'westeurope',
        'properties' => ['provisioningState' => 'Succeeded'],
        'tags' => ['project' => 'test'],
    ];
}

function deploymentFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Resources/deployments/tenantflow',
        'name' => 'tenantflow',
        'properties' => [
            'provisioningState' => 'Running',
            'timestamp' => '2026-01-01T00:00:00.0000000Z',
            'mode' => 'Incremental',
        ],
    ];
}

function secretFixture(): array
{
    return [
        'id' => 'https://vault.vault.azure.net/secrets/webhook-token/abc',
        'name' => 'webhook-token',
        'value' => 'secret-value',
        'attributes' => ['enabled' => true],
    ];
}

function subscriptionAliasFixture(): array
{
    return [
        'id' => '/providers/Microsoft.Subscription/aliases/tenant-acme',
        'name' => 'tenant-acme',
        'type' => 'Microsoft.Subscription/aliases',
        'properties' => [
            'subscriptionId' => '00000000-0000-0000-0000-000000000099',
            'provisioningState' => 'Accepted',
            'billingScope' => '/providers/Microsoft.Billing/billingAccounts/123/enrollmentAccounts/456',
            'displayName' => 'Acme Tenant',
            'workload' => 'Production',
        ],
    ];
}

function roleAssignmentFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/providers/Microsoft.Authorization/roleAssignments/abc',
        'name' => 'abc',
        'properties' => [
            'scope' => '/subscriptions/sub-1/resourceGroups/rg-test',
            'roleDefinitionId' => '/subscriptions/sub-1/providers/Microsoft.Authorization/roleDefinitions/123',
            'principalId' => '00000000-0000-0000-0000-000000000010',
            'principalType' => 'ServicePrincipal',
        ],
    ];
}

function deploymentOperationFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Resources/deployments/tenantflow/operations/op-1',
        'properties' => [
            'operationId' => 'op-1',
            'provisioningState' => 'Succeeded',
            'statusMessage' => 'OK',
            'targetResource' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1',
        ],
    ];
}

function groupFixture(): array
{
    return [
        'id' => 'group-1',
        'displayName' => 'Readers',
        'mailNickname' => 'readers',
        'mailEnabled' => false,
        'securityEnabled' => true,
        'groupTypes' => ['Unified'],
    ];
}

function userFixture(): array
{
    return [
        'id' => 'user-1',
        'displayName' => 'Jane Doe',
        'userPrincipalName' => 'jane@example.test',
        'mail' => 'jane@example.test',
    ];
}

function applicationFixture(): array
{
    return [
        'id' => 'app-object-1',
        'appId' => '00000000-0000-0000-0000-000000000010',
        'displayName' => 'My App',
    ];
}

function passwordCredentialFixture(): array
{
    return [
        'secretText' => 'generated-secret',
        'keyId' => '00000000-0000-0000-0000-000000000011',
        'displayName' => 'default',
    ];
}

function servicePrincipalFixture(): array
{
    return [
        'id' => 'sp-object-1',
        'appId' => '00000000-0000-0000-0000-000000000010',
        'displayName' => 'My App Service Principal',
    ];
}

function invitationFixture(): array
{
    return [
        'id' => 'inv-1',
        'inviteRedeemUrl' => 'https://login.microsoftonline.com/redeem',
        'invitedUserEmailAddress' => 'guest@example.test',
        'status' => 'PendingAcceptance',
        'invitedUser' => userFixture(),
    ];
}

function sqlFirewallRuleFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/firewallRules/deployer-migrate',
        'name' => 'deployer-migrate',
        'properties' => [
            'startIpAddress' => '1.2.3.4',
            'endIpAddress' => '1.2.3.4',
        ],
    ];
}

function sqlDatabaseFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/databases/datalogs',
        'name' => 'datalogs',
        'location' => 'westeurope',
        'properties' => [
            'status' => 'Online',
        ],
    ];
}

function deletedVaultFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/providers/Microsoft.KeyVault/locations/westeurope/deletedVaults/kv-test',
        'name' => 'kv-test',
        'location' => 'westeurope',
        'properties' => [
            'deletionDate' => '2026-01-01T00:00:00Z',
        ],
    ];
}

function secretIdentifierFixture(): array
{
    return [
        'id' => 'https://myvault.vault.azure.net/secrets/webhook-token/abc123',
        'attributes' => ['enabled' => true],
    ];
}

function accessTokenResponseFixture(): array
{
    return [
        'token_type' => 'Bearer',
        'expires_in' => 3600,
        'access_token' => 'eyJ.test.token',
    ];
}

function canceledSubscriptionFixture(): array
{
    return ['subscriptionId' => 'sub-1'];
}
