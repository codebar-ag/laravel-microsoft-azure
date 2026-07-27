<?php

use CodebarAg\MicrosoftAzure\Data\Arm\PostgresConfigurationData;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresFirewallRuleData;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresFlexibleServerData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\CreateOrUpdatePostgresDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\CreateOrUpdatePostgresFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\CreateOrUpdatePostgresFlexibleServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\DeletePostgresDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\DeletePostgresFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\DeletePostgresFlexibleServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresConfiguration;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresFlexibleServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresConfigurations;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresDatabases;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresFirewallRules;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresFlexibleServersByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\UpdatePostgresConfiguration;
use CodebarAg\MicrosoftAzure\Resources\PostgresFirewallRulesResource;
use CodebarAg\MicrosoftAzure\Resources\PostgresFlexibleServersResource;
use Saloon\Http\Faking\MockResponse;

function postgresServerFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.DBforPostgreSQL/flexibleServers/pg1',
        'name' => 'pg1',
        'location' => 'switzerlandnorth',
        'sku' => ['name' => 'Standard_D2ds_v5', 'tier' => 'GeneralPurpose'],
        'properties' => [
            'fullyQualifiedDomainName' => 'pg1.postgres.database.azure.com',
            'state' => 'Ready',
            'version' => '17',
            'administratorLogin' => 'pgadmin',
            'storage' => ['storageSizeGB' => 64],
        ],
    ];
}

function postgresServers(array $responses): PostgresFlexibleServersResource
{
    return new PostgresFlexibleServersResource(clientWithArmMock($responses), 'sub-1', 'rg-test');
}

it('lists postgres flexible servers', function (): void {
    $servers = postgresServers([
        ListPostgresFlexibleServersByResourceGroup::class => MockResponse::make(body: ['value' => [postgresServerFixture()]]),
    ])->list();

    expect($servers)->toHaveCount(1)
        ->and($servers->first())->toBeInstanceOf(PostgresFlexibleServerData::class)
        ->and($servers->first()?->name)->toBe('pg1')
        ->and($servers->first()?->fullyQualifiedDomainName)->toBe('pg1.postgres.database.azure.com')
        ->and($servers->first()?->skuName)->toBe('Standard_D2ds_v5')
        ->and($servers->first()?->skuTier)->toBe('GeneralPurpose')
        ->and($servers->first()?->storageSizeGB)->toBe(64)
        ->and($servers->first()?->version)->toBe('17')
        ->and($servers->first()?->administratorLogin)->toBe('pgadmin');
});

it('gets a postgres flexible server and reports readiness from state', function (): void {
    $server = postgresServers([
        GetPostgresFlexibleServer::class => MockResponse::make(body: postgresServerFixture()),
    ])->server('pg1')->get();

    expect($server->name)->toBe('pg1')
        ->and($server->state)->toBe('Ready')
        ->and($server->isReady())->toBeTrue();
});

it('does not report readiness while the server is still starting', function (): void {
    $fixture = postgresServerFixture();
    $fixture['properties']['state'] = 'Starting';

    $server = postgresServers([
        GetPostgresFlexibleServer::class => MockResponse::make(body: $fixture),
    ])->server('pg1')->get();

    expect($server->isReady())->toBeFalse();
});

it('tolerates a server payload with no properties or sku block', function (): void {
    $server = postgresServers([
        GetPostgresFlexibleServer::class => MockResponse::make(body: ['id' => 'x', 'name' => 'pg1', 'location' => 'westeurope']),
    ])->server('pg1')->get();

    expect($server->state)->toBeNull()
        ->and($server->skuName)->toBeNull()
        ->and($server->storageSizeGB)->toBeNull()
        ->and($server->isReady())->toBeFalse();
});

it('creates or updates a postgres flexible server through the servers gateway', function (): void {
    $server = postgresServers([
        CreateOrUpdatePostgresFlexibleServer::class => MockResponse::make(body: postgresServerFixture()),
    ])->createOrUpdate('pg1', 'switzerlandnorth', 'Standard_D2ds_v5', 'GeneralPurpose', 'pgadmin', 'secret', '17', 64);

    expect($server)->toBeInstanceOf(PostgresFlexibleServerData::class)
        ->and($server->name)->toBe('pg1');
});

it('deletes a postgres flexible server', function (): void {
    $resource = postgresServers([
        DeletePostgresFlexibleServer::class => MockResponse::make(body: '', status: 200),
    ])->server('pg1');

    $resource->delete();

    expect(true)->toBeTrue();
});

it('lists, gets, creates and deletes databases', function (): void {
    $fixture = [
        'id' => '/subscriptions/sub-1/.../databases/tenant_acme',
        'name' => 'tenant_acme',
        'properties' => ['charset' => 'UTF8', 'collation' => 'en_US.utf8'],
    ];

    $databases = postgresServers([
        ListPostgresDatabases::class => MockResponse::make(body: ['value' => [$fixture]]),
        GetPostgresDatabase::class => MockResponse::make(body: $fixture),
        CreateOrUpdatePostgresDatabase::class => MockResponse::make(body: $fixture),
        DeletePostgresDatabase::class => MockResponse::make(body: '', status: 200),
    ])->server('pg1')->databases();

    expect($databases->list())->toHaveCount(1)
        ->and($databases->list()->first())->toBeInstanceOf(PostgresDatabaseData::class)
        ->and($databases->get('tenant_acme')->name)->toBe('tenant_acme')
        ->and($databases->get('tenant_acme')->charset)->toBe('UTF8')
        ->and($databases->get('tenant_acme')->collation)->toBe('en_US.utf8')
        ->and($databases->createOrUpdate('tenant_acme')->name)->toBe('tenant_acme');

    $databases->delete('tenant_acme');
});

it('lists, gets, creates and deletes firewall rules', function (): void {
    $fixture = [
        'id' => '/subscriptions/sub-1/.../firewallRules/office',
        'name' => 'office',
        'properties' => ['startIpAddress' => '1.2.3.4', 'endIpAddress' => '1.2.3.9'],
    ];

    $rules = postgresServers([
        ListPostgresFirewallRules::class => MockResponse::make(body: ['value' => [$fixture]]),
        GetPostgresFirewallRule::class => MockResponse::make(body: $fixture),
        CreateOrUpdatePostgresFirewallRule::class => MockResponse::make(body: $fixture),
        DeletePostgresFirewallRule::class => MockResponse::make(body: '', status: 200),
    ])->server('pg1')->firewallRules();

    expect($rules->list())->toHaveCount(1)
        ->and($rules->list()->first())->toBeInstanceOf(PostgresFirewallRuleData::class)
        ->and($rules->get('office')->startIpAddress)->toBe('1.2.3.4')
        ->and($rules->get('office')->endIpAddress)->toBe('1.2.3.9')
        ->and($rules->createOrUpdate('office', '1.2.3.4', '1.2.3.9')->name)->toBe('office');

    $rules->delete('office');
});

it('allows azure services with the 0.0.0.0 sentinel rule', function (): void {
    $rules = postgresServers([
        CreateOrUpdatePostgresFirewallRule::class => MockResponse::make(body: [
            'id' => 'x',
            'name' => 'AllowAllAzureServices',
            'properties' => ['startIpAddress' => '0.0.0.0', 'endIpAddress' => '0.0.0.0'],
        ]),
    ])->server('pg1')->firewallRules();

    $rule = $rules->allowAzureServices();

    expect($rule->name)->toBe('AllowAllAzureServices')
        ->and($rule->startIpAddress)->toBe(PostgresFirewallRulesResource::ALLOW_AZURE_SERVICES_IP)
        ->and($rule->endIpAddress)->toBe(PostgresFirewallRulesResource::ALLOW_AZURE_SERVICES_IP);
});

it('lists, gets and updates server configurations', function (): void {
    $fixture = [
        'id' => '/subscriptions/sub-1/.../configurations/require_secure_transport',
        'name' => 'require_secure_transport',
        'properties' => [
            'value' => 'ON',
            'defaultValue' => 'ON',
            'dataType' => 'Boolean',
            'allowedValues' => 'on,off',
            'source' => 'user-override',
            'isDynamicConfig' => false,
            'isReadOnly' => false,
        ],
    ];

    $configurations = postgresServers([
        ListPostgresConfigurations::class => MockResponse::make(body: ['value' => [$fixture]]),
        GetPostgresConfiguration::class => MockResponse::make(body: $fixture),
        UpdatePostgresConfiguration::class => MockResponse::make(body: $fixture),
    ])->server('pg1')->configurations();

    $configuration = $configurations->get('require_secure_transport');

    expect($configurations->list())->toHaveCount(1)
        ->and($configurations->list()->first())->toBeInstanceOf(PostgresConfigurationData::class)
        ->and($configuration->value)->toBe('ON')
        ->and($configuration->defaultValue)->toBe('ON')
        ->and($configuration->dataType)->toBe('Boolean')
        ->and($configuration->allowedValues)->toBe('on,off')
        ->and($configuration->source)->toBe('user-override')
        ->and($configuration->isDynamicConfig)->toBeFalse()
        ->and($configuration->isReadOnly)->toBeFalse()
        ->and($configuration->requiresRestart())->toBeTrue()
        ->and($configurations->update('require_secure_transport', 'ON')->value)->toBe('ON')
        ->and($configurations->requireSecureTransport()->value)->toBe('ON');
});

it('reports a dynamic configuration as not requiring a restart', function (): void {
    $configurations = postgresServers([
        GetPostgresConfiguration::class => MockResponse::make(body: [
            'id' => 'x',
            'name' => 'max_connections',
            'properties' => ['value' => '100', 'isDynamicConfig' => true],
        ]),
    ])->server('pg1')->configurations();

    expect($configurations->get('max_connections')->requiresRestart())->toBeFalse();
});

it('turns secure transport off when asked', function (): void {
    $configurations = postgresServers([
        UpdatePostgresConfiguration::class => MockResponse::make(body: [
            'id' => 'x',
            'name' => 'require_secure_transport',
            'properties' => ['value' => 'OFF'],
        ]),
    ])->server('pg1')->configurations();

    expect($configurations->requireSecureTransport(false)->value)->toBe('OFF');
});

it('exposes postgres flexible servers from the client resource gateway', function (): void {
    $client = clientWithArmMock([
        ListPostgresFlexibleServersByResourceGroup::class => MockResponse::make(body: ['value' => []]),
    ]);

    expect($client->postgresFlexibleServers('sub-1', 'rg-test'))
        ->toBeInstanceOf(PostgresFlexibleServersResource::class)
        ->and($client->postgresFlexibleServers('sub-1', 'rg-test')->list())->toHaveCount(0);
});
