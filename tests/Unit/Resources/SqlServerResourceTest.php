<?php

use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlServerData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\ListSqlServersByResourceGroup;
use CodebarAg\MicrosoftAzure\Resources\SqlDatabasesResource;
use CodebarAg\MicrosoftAzure\Resources\SqlServersResource;
use Saloon\Http\Faking\MockResponse;

function sqlServerFixture(): array
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

it('lists sql servers via servers resource gateway', function (): void {
    $client = clientWithArmMock([
        ListSqlServersByResourceGroup::class => MockResponse::make(body: ['value' => [sqlServerFixture()]]),
    ]);

    $servers = (new SqlServersResource($client, 'sub-1', 'rg-test'))->list();

    expect($servers)->toHaveCount(1)
        ->and($servers->first())->toBeInstanceOf(SqlServerData::class)
        ->and($servers->first()?->name)->toBe('sql1')
        ->and($servers->first()?->fullyQualifiedDomainName)->toBe('sql1.database.windows.net')
        ->and($servers->first()?->state)->toBe('Ready')
        ->and($servers->first()?->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('gets a sql server via server resource gateway', function (): void {
    $client = clientWithArmMock([
        GetSqlServer::class => MockResponse::make(body: sqlServerFixture()),
    ]);

    $server = (new SqlServersResource($client, 'sub-1', 'rg-test'))->server('sql1')->get();

    expect($server)->toBeInstanceOf(SqlServerData::class)
        ->and($server->name)->toBe('sql1');
});

it('creates or updates a sql server via servers resource gateway', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateSqlServer::class => MockResponse::make(body: sqlServerFixture()),
    ]);

    $server = (new SqlServersResource($client, 'sub-1', 'rg-test'))->createOrUpdate('sql1', 'westeurope', 'sqladmin');

    expect($server)->toBeInstanceOf(SqlServerData::class)
        ->and($server->name)->toBe('sql1');
});

it('deletes a sql server via server resource gateway', function (): void {
    $client = clientWithArmMock([
        DeleteSqlServer::class => MockResponse::make(body: '', status: 200),
    ]);

    (new SqlServersResource($client, 'sub-1', 'rg-test'))->server('sql1')->delete();
})->throwsNoExceptions();

it('creates or updates a sql database and maps enriched fields', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateSqlDatabase::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/databases/datalogs',
            'name' => 'datalogs',
            'location' => 'westeurope',
            'properties' => [
                'status' => 'Online',
                'collation' => 'SQL_Latin1_General_CP1_CI_AS',
                'currentServiceObjectiveName' => 'GP_S_Gen5_2',
                'autoPauseDelay' => 60,
            ],
        ]),
    ]);

    $database = (new SqlDatabasesResource($client, 'sub-1', 'rg-test', 'sql1'))
        ->createOrUpdate('datalogs', 'westeurope');

    expect($database)->toBeInstanceOf(SqlDatabaseData::class)
        ->and($database->name)->toBe('datalogs')
        ->and($database->status)->toBe(ProvisioningState::tryFrom('Online'))
        ->and($database->collation)->toBe('SQL_Latin1_General_CP1_CI_AS')
        ->and($database->edition)->toBe('GP_S_Gen5_2')
        ->and($database->currentServiceObjectiveName)->toBe('GP_S_Gen5_2')
        ->and($database->autoPauseDelay)->toBe(60);
});

it('deletes a sql database via databases resource gateway', function (): void {
    $client = clientWithArmMock([
        DeleteSqlDatabase::class => MockResponse::make(body: '', status: 200),
    ]);

    (new SqlDatabasesResource($client, 'sub-1', 'rg-test', 'sql1'))->delete('datalogs');
})->throwsNoExceptions();
