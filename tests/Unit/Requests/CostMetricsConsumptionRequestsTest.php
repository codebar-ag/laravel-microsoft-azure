<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Consumption\ListUsageDetails;
use CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost;
use CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\GetMetrics;
use CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\ListMetricDefinitions;
use Saloon\Http\Request;

dataset('cost metrics consumption endpoints', [
    'QueryCost (subscription scope)' => [
        fn () => new QueryCost('subscriptions/sub-1', new CostQueryPayload('2026-01-01', '2026-01-31')),
        '/subscriptions/sub-1/providers/Microsoft.CostManagement/query',
        ApiVersion::ARM_COST_MANAGEMENT,
    ],
    'QueryCost (resource group scope, leading slash)' => [
        fn () => new QueryCost('/subscriptions/sub-1/resourceGroups/rg-test', new CostQueryPayload('2026-01-01', '2026-01-31')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CostManagement/query',
        ApiVersion::ARM_COST_MANAGEMENT,
    ],
    'GetMetrics' => [
        fn () => new GetMetrics('subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/app1', ['Requests'], 'PT1H'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/app1/providers/Microsoft.Insights/metrics',
        ApiVersion::ARM_MONITOR_METRICS,
    ],
    'ListMetricDefinitions' => [
        fn () => new ListMetricDefinitions('subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/app1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/app1/providers/Microsoft.Insights/metricDefinitions',
        ApiVersion::ARM_MONITOR_METRICS,
    ],
    'ListUsageDetails' => [
        fn () => new ListUsageDetails('subscriptions/sub-1'),
        '/subscriptions/sub-1/providers/Microsoft.Consumption/usageDetails',
        ApiVersion::ARM_CONSUMPTION,
    ],
]);

it('resolves endpoint and api-version for cost/metrics/consumption requests', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all()['api-version'])->toBe($apiVersion);
})->with('cost metrics consumption endpoints');

it('adds metricnames, timespan and aggregation to GetMetrics query', function (): void {
    $request = new GetMetrics(
        'subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/app1',
        ['Requests', 'Http5xx'],
        'PT1H',
        'PT5M',
    );

    $query = $request->query()->all();

    expect($query['metricnames'])->toBe('Requests,Http5xx')
        ->and($query['timespan'])->toBe('PT1H')
        ->and($query['aggregation'])->toBe('Total')
        ->and($query['interval'])->toBe('PT5M');
});

it('omits interval from GetMetrics query when null', function (): void {
    $request = new GetMetrics('subscriptions/sub-1/providers/Microsoft.Web/sites/app1', ['Requests'], 'PT1H');

    expect($request->query()->all())->not->toHaveKey('interval');
});

it('adds $filter to ListUsageDetails query when provided', function (): void {
    $request = new ListUsageDetails('subscriptions/sub-1', "properties/usageStart ge '2026-01-01'");

    $query = $request->query()->all();

    expect($query)->toHaveKey('$filter', "properties/usageStart ge '2026-01-01'")
        ->and($query['api-version'])->toBe(ApiVersion::ARM_CONSUMPTION);
});

it('builds the QueryCost body from the payload', function (): void {
    $request = new QueryCost('subscriptions/sub-1', new CostQueryPayload('2026-01-01', '2026-01-31', 'ResourceGroup'));

    expect($request->body()->all())->toMatchArray([
        'type' => 'ActualCost',
        'timeframe' => 'Custom',
        'timePeriod' => ['from' => '2026-01-01', 'to' => '2026-01-31'],
        'dataset' => [
            'granularity' => 'None',
            'aggregation' => ['totalCost' => ['name' => 'Cost', 'function' => 'Sum']],
            'grouping' => [['type' => 'Dimension', 'name' => 'ResourceGroup']],
        ],
    ]);
});
