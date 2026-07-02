<?php

use CodebarAg\MicrosoftAzure\Data\LogAnalytics\QueryResultsData;
use CodebarAg\MicrosoftAzure\Data\LogAnalytics\QueryTableData;
use CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsQueryPayload;
use CodebarAg\MicrosoftAzure\Requests\LogAnalytics\ExecuteWorkspaceQuery;
use CodebarAg\MicrosoftAzure\Resources\LogAnalyticsQueryResource;
use Saloon\Http\Faking\MockResponse;

it('resolves the workspace query endpoint without an api-version', function (): void {
    $request = new ExecuteWorkspaceQuery('ws-1', new LogAnalyticsQueryPayload('Usage | take 1', 'P1D'));

    expect($request->resolveEndpoint())->toBe('/workspaces/ws-1/query')
        ->and($request->query()->all())->toBe([])
        ->and($request->body()->all())->toBe(['query' => 'Usage | take 1', 'timespan' => 'P1D']);
});

it('omits the timespan when not provided', function (): void {
    $request = new ExecuteWorkspaceQuery('ws-1', new LogAnalyticsQueryPayload('Usage | take 1'));

    expect($request->body()->all())->toBe(['query' => 'Usage | take 1']);
});

it('executes a kql query and maps tables, columns and rows', function (): void {
    $client = clientWithLogAnalyticsMock([
        ExecuteWorkspaceQuery::class => MockResponse::make(body: [
            'tables' => [
                [
                    'name' => 'PrimaryResult',
                    'columns' => [
                        ['name' => 'TimeGenerated', 'type' => 'datetime'],
                        ['name' => 'Quantity', 'type' => 'real'],
                    ],
                    'rows' => [
                        ['2026-01-01T00:00:00Z', 12.5],
                        ['2026-01-01T01:00:00Z', 7.25],
                    ],
                ],
            ],
        ]),
    ]);

    $results = $client->logAnalytics()->query('ws-1', 'Usage | take 2', 'P1D');

    expect($results)->toBeInstanceOf(QueryResultsData::class)
        ->and($results->tables)->toHaveCount(1)
        ->and($results->table())->toBeInstanceOf(QueryTableData::class)
        ->and($results->table()?->columns)->toBe(['TimeGenerated', 'Quantity'])
        ->and($results->table()?->rows)->toHaveCount(2)
        ->and($results->rowsAssoc())->toBe([
            ['TimeGenerated' => '2026-01-01T00:00:00Z', 'Quantity' => 12.5],
            ['TimeGenerated' => '2026-01-01T01:00:00Z', 'Quantity' => 7.25],
        ])
        ->and($results->table('Missing'))->toBeNull()
        ->and($results->rowsAssoc('Missing'))->toBe([]);
});

it('creates the query resource from the fluent entry point', function (): void {
    $client = clientWithSeededToken();

    expect($client->logAnalytics())->toBeInstanceOf(LogAnalyticsQueryResource::class);
});
