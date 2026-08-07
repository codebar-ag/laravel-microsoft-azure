<?php

use CodebarAg\MicrosoftAzure\Data\Arm\CostQueryResultData;
use CodebarAg\MicrosoftAzure\Enums\CostGranularity;
use CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost;
use CodebarAg\MicrosoftAzure\Resources\CostManagementResource;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

it('maps cost query rows and detects currency', function (): void {
    $client = clientWithArmMock([
        QueryCost::class => MockResponse::make(body: [
            'properties' => [
                'columns' => [
                    ['name' => 'Cost', 'type' => 'Number'],
                    ['name' => 'ServiceName', 'type' => 'String'],
                    ['name' => 'Currency', 'type' => 'String'],
                ],
                'rows' => [
                    [12.5, 'Storage', 'CHF'],
                    [4.25, 'Compute', 'CHF'],
                ],
            ],
        ]),
    ]);

    $result = (new CostManagementResource($client, 'subscriptions/sub-1/resourceGroups/rg-test'))
        ->query('2026-01-01', '2026-01-31');

    expect($result)->toBeInstanceOf(CostQueryResultData::class)
        ->and($result->columns)->toBe(['Cost', 'ServiceName', 'Currency'])
        ->and($result->rows)->toHaveCount(2)
        ->and($result->rows[0])->toBe(['Cost' => 12.5, 'ServiceName' => 'Storage', 'Currency' => 'CHF'])
        ->and($result->rows[1]['ServiceName'])->toBe('Compute')
        ->and($result->currency)->toBe('CHF');
});

it('returns null currency when no currency column is present', function (): void {
    $client = clientWithArmMock([
        QueryCost::class => MockResponse::make(body: [
            'properties' => [
                'columns' => [
                    ['name' => 'Cost', 'type' => 'Number'],
                    ['name' => 'ServiceName', 'type' => 'String'],
                ],
                'rows' => [
                    [99.5, 'Storage'],
                ],
            ],
        ]),
    ]);

    $result = (new CostManagementResource($client, 'subscriptions/sub-1'))
        ->query('2026-01-01', '2026-01-31');

    expect($result->currency)->toBeNull()
        ->and($result->rows[0])->toBe(['Cost' => 99.5, 'ServiceName' => 'Storage']);
});

it('sends the granularity and groupings the caller asked for', function (): void {
    // Asserted on the request body rather than only the mapped result: the
    // whole point of this surface is what it asks Azure for, and a resource
    // that mapped the response perfectly while querying the wrong shape would
    // pass every other test here.
    $mock = new MockClient([
        QueryCost::class => MockResponse::make(body: ['properties' => ['columns' => [], 'rows' => []]]),
    ]);

    $client = clientWithSeededToken();
    $client->arm()->withMockClient($mock);

    (new CostManagementResource($client, 'subscriptions/sub-1/resourceGroups/rg-test'))
        ->query('2026-07-01', '2026-07-31', ['ResourceGroupName', 'ServiceName'], CostGranularity::Daily);

    $body = $mock->getLastPendingRequest()?->body()?->all();

    expect($body['dataset']['granularity'])->toBe('Daily')
        ->and($body['dataset']['grouping'])->toBe([
            ['type' => 'Dimension', 'name' => 'ResourceGroupName'],
            ['type' => 'Dimension', 'name' => 'ServiceName'],
        ]);
});

it('maps a daily series, one row per day', function (): void {
    // Daily adds a UsageDate column (yyyyMMdd, as an integer) and multiplies
    // the row count — the reason it is opt-in rather than the default.
    $client = clientWithArmMock([
        QueryCost::class => MockResponse::make(body: [
            'properties' => [
                'columns' => [
                    ['name' => 'Cost', 'type' => 'Number'],
                    ['name' => 'UsageDate', 'type' => 'Number'],
                    ['name' => 'ServiceName', 'type' => 'String'],
                    ['name' => 'Currency', 'type' => 'String'],
                ],
                'rows' => [
                    [1.5, 20260701, 'Storage', 'CHF'],
                    [2.5, 20260702, 'Storage', 'CHF'],
                ],
            ],
        ]),
    ]);

    $result = (new CostManagementResource($client, 'subscriptions/sub-1'))
        ->queryDaily('2026-07-01', '2026-07-31');

    expect($result->columns)->toContain('UsageDate')
        ->and($result->rows)->toHaveCount(2)
        ->and($result->rows[0]['UsageDate'])->toBe(20260701)
        ->and($result->rows[1]['Cost'])->toBe(2.5);
});

it('queryDaily is the daily variant of query', function (): void {
    $mock = new MockClient([
        QueryCost::class => MockResponse::make(body: ['properties' => ['columns' => [], 'rows' => []]]),
    ]);

    $client = clientWithSeededToken();
    $client->arm()->withMockClient($mock);

    (new CostManagementResource($client, 'subscriptions/sub-1'))->queryDaily('2026-07-01', '2026-07-31');

    $body = $mock->getLastPendingRequest()?->body()?->all();

    expect($body['dataset']['granularity'])->toBe('Daily')
        ->and($body['dataset']['grouping'])->toBe([['type' => 'Dimension', 'name' => 'ServiceName']]);
});
