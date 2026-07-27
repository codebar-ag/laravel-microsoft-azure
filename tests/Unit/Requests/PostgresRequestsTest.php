<?php

use CodebarAg\MicrosoftAzure\Data\Payload\PostgresConfigurationPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresDatabasePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresFirewallRulePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresFlexibleServerPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
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
use Saloon\Http\Request;

const PG_SERVER_PATH = '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.DBforPostgreSQL/flexibleServers/pg1';

dataset('postgres request endpoints', [
    'ListPostgresFlexibleServersByResourceGroup' => [
        fn () => new ListPostgresFlexibleServersByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.DBforPostgreSQL/flexibleServers',
    ],
    'GetPostgresFlexibleServer' => [
        fn () => new GetPostgresFlexibleServer('sub-1', 'rg-test', 'pg1'),
        PG_SERVER_PATH,
    ],
    'CreateOrUpdatePostgresFlexibleServer' => [
        fn () => new CreateOrUpdatePostgresFlexibleServer(
            'sub-1',
            'rg-test',
            'pg1',
            new PostgresFlexibleServerPayload('westeurope', 'Standard_B1ms', 'Burstable'),
        ),
        PG_SERVER_PATH,
    ],
    'DeletePostgresFlexibleServer' => [
        fn () => new DeletePostgresFlexibleServer('sub-1', 'rg-test', 'pg1'),
        PG_SERVER_PATH,
    ],
    'ListPostgresDatabases' => [
        fn () => new ListPostgresDatabases('sub-1', 'rg-test', 'pg1'),
        PG_SERVER_PATH.'/databases',
    ],
    'GetPostgresDatabase' => [
        fn () => new GetPostgresDatabase('sub-1', 'rg-test', 'pg1', 'tenant_acme'),
        PG_SERVER_PATH.'/databases/tenant_acme',
    ],
    'CreateOrUpdatePostgresDatabase' => [
        fn () => new CreateOrUpdatePostgresDatabase(
            'sub-1',
            'rg-test',
            'pg1',
            'tenant_acme',
            new PostgresDatabasePayload,
        ),
        PG_SERVER_PATH.'/databases/tenant_acme',
    ],
    'DeletePostgresDatabase' => [
        fn () => new DeletePostgresDatabase('sub-1', 'rg-test', 'pg1', 'tenant_acme'),
        PG_SERVER_PATH.'/databases/tenant_acme',
    ],
    'ListPostgresFirewallRules' => [
        fn () => new ListPostgresFirewallRules('sub-1', 'rg-test', 'pg1'),
        PG_SERVER_PATH.'/firewallRules',
    ],
    'GetPostgresFirewallRule' => [
        fn () => new GetPostgresFirewallRule('sub-1', 'rg-test', 'pg1', 'office'),
        PG_SERVER_PATH.'/firewallRules/office',
    ],
    'CreateOrUpdatePostgresFirewallRule' => [
        fn () => new CreateOrUpdatePostgresFirewallRule(
            'sub-1',
            'rg-test',
            'pg1',
            'office',
            new PostgresFirewallRulePayload('1.2.3.4', '1.2.3.4'),
        ),
        PG_SERVER_PATH.'/firewallRules/office',
    ],
    'DeletePostgresFirewallRule' => [
        fn () => new DeletePostgresFirewallRule('sub-1', 'rg-test', 'pg1', 'office'),
        PG_SERVER_PATH.'/firewallRules/office',
    ],
    'ListPostgresConfigurations' => [
        fn () => new ListPostgresConfigurations('sub-1', 'rg-test', 'pg1'),
        PG_SERVER_PATH.'/configurations',
    ],
    'GetPostgresConfiguration' => [
        fn () => new GetPostgresConfiguration('sub-1', 'rg-test', 'pg1', 'require_secure_transport'),
        PG_SERVER_PATH.'/configurations/require_secure_transport',
    ],
    'UpdatePostgresConfiguration' => [
        fn () => new UpdatePostgresConfiguration(
            'sub-1',
            'rg-test',
            'pg1',
            'require_secure_transport',
            new PostgresConfigurationPayload('ON'),
        ),
        PG_SERVER_PATH.'/configurations/require_secure_transport',
    ],
]);

it('resolves postgres request endpoints and api-version query', function (callable $factory, string $endpoint): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => ApiVersion::ARM_POSTGRESQL->value()]);
})->with('postgres request endpoints');

it('pins the postgres api version to the current stable release', function (): void {
    expect(ApiVersion::ARM_POSTGRESQL->value())->toBe('2025-08-01');
});

it('builds a flexible server body with sku at the top level, not under properties', function (): void {
    $request = new CreateOrUpdatePostgresFlexibleServer(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'pg1',
        payload: new PostgresFlexibleServerPayload(
            location: 'switzerlandnorth',
            skuName: 'Standard_D2ds_v5',
            skuTier: 'GeneralPurpose',
            administratorLogin: 'pgadmin',
            administratorLoginPassword: 'secret',
            version: '17',
            storageSizeGB: 64,
            properties: ['network' => ['publicNetworkAccess' => 'Enabled']],
            tags: ['env' => 'prod'],
        ),
    );

    expect($request->body()->all())->toBe([
        'location' => 'switzerlandnorth',
        'sku' => ['name' => 'Standard_D2ds_v5', 'tier' => 'GeneralPurpose'],
        'properties' => [
            'network' => ['publicNetworkAccess' => 'Enabled'],
            'administratorLogin' => 'pgadmin',
            'administratorLoginPassword' => 'secret',
            'version' => '17',
            'storage' => ['storageSizeGB' => 64],
        ],
        'tags' => ['env' => 'prod'],
    ]);
});

it('omits optional flexible server properties and tags when not provided', function (): void {
    $request = new CreateOrUpdatePostgresFlexibleServer(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'pg1',
        payload: new PostgresFlexibleServerPayload('westeurope', 'Standard_B1ms', 'Burstable'),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'sku' => ['name' => 'Standard_B1ms', 'tier' => 'Burstable'],
        'properties' => [],
    ]);
});

it('merges storageSizeGB into a caller-supplied storage block rather than replacing it', function (): void {
    $payload = new PostgresFlexibleServerPayload(
        location: 'westeurope',
        skuName: 'Standard_B1ms',
        skuTier: 'Burstable',
        storageSizeGB: 128,
        properties: ['storage' => ['autoGrow' => 'Enabled']],
    );

    expect($payload->toAzureBody()['properties']['storage'])
        ->toBe(['autoGrow' => 'Enabled', 'storageSizeGB' => 128]);
});

it('keeps a caller-supplied storageSizeGB when the shorthand is not used', function (): void {
    $payload = new PostgresFlexibleServerPayload(
        location: 'westeurope',
        skuName: 'Standard_B1ms',
        skuTier: 'Burstable',
        properties: ['storage' => ['storageSizeGB' => 32]],
    );

    expect($payload->toAzureBody()['properties']['storage'])->toBe(['storageSizeGB' => 32]);
});

it('builds a database body with azure default charset and collation', function (): void {
    $request = new CreateOrUpdatePostgresDatabase('sub-1', 'rg-test', 'pg1', 'tenant_acme', new PostgresDatabasePayload);

    expect($request->body()->all())->toBe([
        'properties' => ['charset' => 'UTF8', 'collation' => 'en_US.utf8'],
    ])
        ->and((new PostgresDatabasePayload('LATIN1', 'de_CH.utf8'))->toAzureBody())->toBe([
            'properties' => ['charset' => 'LATIN1', 'collation' => 'de_CH.utf8'],
        ]);
});

it('builds a firewall rule body', function (): void {
    $request = new CreateOrUpdatePostgresFirewallRule(
        'sub-1',
        'rg-test',
        'pg1',
        'office',
        new PostgresFirewallRulePayload('10.0.0.1', '10.0.0.9'),
    );

    expect($request->body()->all())->toBe([
        'properties' => ['startIpAddress' => '10.0.0.1', 'endIpAddress' => '10.0.0.9'],
    ]);
});

it('builds a configuration body defaulting source to user-override', function (): void {
    $request = new UpdatePostgresConfiguration(
        'sub-1',
        'rg-test',
        'pg1',
        'require_secure_transport',
        new PostgresConfigurationPayload('ON'),
    );

    expect($request->body()->all())->toBe([
        'properties' => ['value' => 'ON', 'source' => 'user-override'],
    ])
        ->and((new PostgresConfigurationPayload('OFF', 'system-default'))->toAzureBody())->toBe([
            'properties' => ['value' => 'OFF', 'source' => 'system-default'],
        ]);
});
