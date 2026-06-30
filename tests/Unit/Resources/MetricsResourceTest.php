<?php

use CodebarAg\MicrosoftAzure\Data\Arm\MetricResultData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\GetMetrics;
use CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\ListMetricDefinitions;
use CodebarAg\MicrosoftAzure\Resources\MetricsResource;
use Saloon\Http\Faking\MockResponse;

it('maps metric results with name, unit and points', function (): void {
    $client = clientWithArmMock([
        GetMetrics::class => MockResponse::make(body: [
            'value' => [
                [
                    'name' => ['value' => 'Requests', 'localizedValue' => 'Requests'],
                    'unit' => 'Count',
                    'timeseries' => [
                        [
                            'data' => [
                                ['timeStamp' => '2026-01-01T00:00:00Z', 'total' => 10.5],
                                ['timeStamp' => '2026-01-01T01:00:00Z', 'total' => 25.5],
                            ],
                        ],
                    ],
                ],
            ],
        ]),
    ]);

    $metrics = (new MetricsResource($client, 'subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/app1'))
        ->get(['Requests'], 'PT2H', 'PT1H');

    expect($metrics)->toHaveCount(1);

    $metric = $metrics->first();

    expect($metric)->toBeInstanceOf(MetricResultData::class)
        ->and($metric->name)->toBe('Requests')
        ->and($metric->unit)->toBe('Count')
        ->and($metric->points)->toHaveCount(2)
        ->and($metric->points[0])->toBe(['timestamp' => '2026-01-01T00:00:00Z', 'total' => 10.5])
        ->and($metric->points[1]['total'])->toBe(25.5);
});

it('lists metric definitions as name/unit arrays', function (): void {
    $client = clientWithArmMock([
        ListMetricDefinitions::class => MockResponse::make(body: [
            'value' => [
                ['name' => ['value' => 'Requests'], 'unit' => 'Count'],
                ['name' => ['value' => 'CpuTime'], 'unit' => 'Seconds'],
            ],
        ]),
    ]);

    $definitions = (new MetricsResource($client, 'subscriptions/sub-1/providers/Microsoft.Web/sites/app1'))
        ->definitions();

    expect($definitions)->toHaveCount(2)
        ->and($definitions->first())->toBe(['name' => 'Requests', 'unit' => 'Count']);
});
