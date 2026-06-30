<?php

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Authentication\AccessTokenData;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;
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
    ->group('integration')
    ->beforeEach(function (): void {
        if (! integrationCredentialsConfigured()) {
            test()->markTestSkipped('MICROSOFT_AZURE_* integration credentials are not configured.');
        }

        configureIntegrationConnection();
    })
    ->in('Integration');

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

/**
 * @param  callable(\CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext): void  $callback
 */
function withLiveResourceGroup(callable $callback): void
{
    $context = \CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext::provisionResourceGroup();

    try {
        $callback($context);
    } finally {
        $context->teardown();
    }
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

function clientWithSeededToken(): AzureClient
{
    $manager = app(MicrosoftAzureManager::class);
    $config = testConnectionConfig();

    $token = new AccessTokenData(
        accessToken: 'seeded-access-token',
        tokenType: 'Bearer',
        expiresIn: 3600,
        expiresAt: Carbon::now()->addHour(),
    );

    Cache::store($config->cacheDriver)
        ->put(
            'microsoft-azure.oauth.'.$config->identifier().'.arm',
            Crypt::encrypt($token),
            3600,
        );

    Cache::store($config->cacheDriver)
        ->put(
            'microsoft-azure.oauth.'.$config->identifier().'.graph',
            Crypt::encrypt($token),
            3600,
        );

    Cache::store($config->cacheDriver)
        ->put(
            'microsoft-azure.oauth.'.$config->identifier().'.key_vault',
            Crypt::encrypt($token),
            3600,
        );

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
