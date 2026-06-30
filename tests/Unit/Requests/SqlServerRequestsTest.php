<?php

use CodebarAg\MicrosoftAzure\Data\Payload\SqlDatabasePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\SqlServerPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\ListSqlServersByResourceGroup;
use Saloon\Http\Request;

dataset('sql server request endpoints', [
    'GetSqlServer' => [
        fn () => new GetSqlServer('sub-1', 'rg-test', 'sql1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1',
        ApiVersion::ARM_SQL,
    ],
    'ListSqlServersByResourceGroup' => [
        fn () => new ListSqlServersByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers',
        ApiVersion::ARM_SQL,
    ],
    'CreateOrUpdateSqlServer' => [
        fn () => new CreateOrUpdateSqlServer('sub-1', 'rg-test', 'sql1', new SqlServerPayload('westeurope')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1',
        ApiVersion::ARM_SQL,
    ],
    'DeleteSqlServer' => [
        fn () => new DeleteSqlServer('sub-1', 'rg-test', 'sql1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1',
        ApiVersion::ARM_SQL,
    ],
    'CreateOrUpdateSqlDatabase' => [
        fn () => new CreateOrUpdateSqlDatabase('sub-1', 'rg-test', 'sql1', 'datalogs', new SqlDatabasePayload('westeurope')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/databases/datalogs',
        ApiVersion::ARM_SQL,
    ],
    'DeleteSqlDatabase' => [
        fn () => new DeleteSqlDatabase('sub-1', 'rg-test', 'sql1', 'datalogs'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/databases/datalogs',
        ApiVersion::ARM_SQL,
    ],
]);

it('resolves sql server request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('sql server request endpoints');

it('builds sql server body with version and optional administrator login', function (): void {
    $request = new CreateOrUpdateSqlServer(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'sql1',
        payload: new SqlServerPayload('westeurope', 'sqladmin', '12.0', ['minimalTlsVersion' => '1.2'], ['env' => 'prod']),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'version' => '12.0',
            'administratorLogin' => 'sqladmin',
            'minimalTlsVersion' => '1.2',
        ],
        'tags' => ['env' => 'prod'],
    ]);
});

it('omits administrator login from sql server body when not provided', function (): void {
    $request = new CreateOrUpdateSqlServer(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'sql1',
        payload: new SqlServerPayload('westeurope'),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => ['version' => '12.0'],
    ]);
});

it('builds sql database body with filtered sku and properties', function (): void {
    $request = new CreateOrUpdateSqlDatabase(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'sql1',
        databaseName: 'datalogs',
        payload: new SqlDatabasePayload('westeurope', 'GP_S_Gen5', 'GeneralPurpose', 'Gen5', 2, ['autoPauseDelay' => 60]),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'sku' => [
            'name' => 'GP_S_Gen5',
            'tier' => 'GeneralPurpose',
            'family' => 'Gen5',
            'capacity' => 2,
        ],
        'properties' => ['autoPauseDelay' => 60],
    ]);
});

it('drops null capacity from sql database sku', function (): void {
    $request = new CreateOrUpdateSqlDatabase(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'sql1',
        databaseName: 'datalogs',
        payload: new SqlDatabasePayload('westeurope'),
    );

    expect($request->body()->all()['sku'])->toBe([
        'name' => 'GP_S_Gen5',
        'tier' => 'GeneralPurpose',
        'family' => 'Gen5',
    ]);
});
