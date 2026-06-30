<?php

use CodebarAg\MicrosoftAzure\Data\Arm\CostQueryResultData;
use CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost;
use CodebarAg\MicrosoftAzure\Resources\CostManagementResource;
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
