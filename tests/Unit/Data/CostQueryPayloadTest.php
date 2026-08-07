<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload;
use CodebarAg\MicrosoftAzure\Enums\CostGranularity;

it('defaults to a single ungranular total per service', function (): void {
    // The shape every existing caller gets. Pinned so adding granularity
    // cannot silently change what they already receive.
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31'))->toAzureBody();

    expect($body)->toBe([
        'type' => 'ActualCost',
        'timeframe' => 'Custom',
        'timePeriod' => ['from' => '2026-01-01', 'to' => '2026-01-31'],
        'dataset' => [
            'granularity' => 'None',
            'aggregation' => ['totalCost' => ['name' => 'Cost', 'function' => 'Sum']],
            'grouping' => [['type' => 'Dimension', 'name' => 'ServiceName']],
        ],
    ]);
});

it('asks for a daily series when told to', function (): void {
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', granularity: CostGranularity::Daily))
        ->toAzureBody();

    expect($body['dataset']['granularity'])->toBe('Daily');
});

it('groups by several dimensions at once', function (): void {
    // Cost Management's own `dataset.grouping` is always an array; a list is
    // the shape that matches the API.
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', ['ResourceGroupName', 'ServiceName', 'Meter']))
        ->toAzureBody();

    expect($body['dataset']['grouping'])->toBe([
        ['type' => 'Dimension', 'name' => 'ResourceGroupName'],
        ['type' => 'Dimension', 'name' => 'ServiceName'],
        ['type' => 'Dimension', 'name' => 'Meter'],
    ]);
});

it('accepts a single dimension as a bare string', function (): void {
    // The original signature, kept because it is the common case.
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', 'ResourceGroupName'))->toAzureBody();

    expect($body['dataset']['grouping'])->toBe([['type' => 'Dimension', 'name' => 'ResourceGroupName']]);
});

it('omits the grouping key entirely rather than sending an empty array', function (): void {
    // Cost Management rejects `grouping: []` with a 400, while omitting the key
    // is valid and means "one total for the whole period" — the only way to ask
    // for that.
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', []))->toAzureBody();

    expect($body['dataset'])->not->toHaveKey('grouping')
        ->and($body['dataset']['granularity'])->toBe('None');
});

it('drops an empty dimension name rather than sending it', function (): void {
    // An empty name is a 400 from ARM, and it is the shape a caller building a
    // list from optional config produces by accident.
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', ['ServiceName', '', 'Meter']))->toAzureBody();

    expect($body['dataset']['grouping'])->toBe([
        ['type' => 'Dimension', 'name' => 'ServiceName'],
        ['type' => 'Dimension', 'name' => 'Meter'],
    ]);
});

it('reindexes after dropping so the grouping stays a json array', function (): void {
    // array_filter preserves keys; without the array_values() the body would
    // encode as a JSON object and ARM would reject it.
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', ['', 'ServiceName']))->toAzureBody();

    expect(array_keys($body['dataset']['grouping']))->toBe([0])
        ->and(json_encode($body['dataset']['grouping']))->toStartWith('[');
});

it('omits grouping when every name was empty', function (): void {
    $body = (new CostQueryPayload('2026-01-01', '2026-01-31', ['', '']))->toAzureBody();

    expect($body['dataset'])->not->toHaveKey('grouping');
});

it('keeps the time period verbatim', function (): void {
    $body = (new CostQueryPayload('2026-07-01', '2026-07-31', granularity: CostGranularity::Daily))
        ->toAzureBody();

    expect($body['timePeriod'])->toBe(['from' => '2026-07-01', 'to' => '2026-07-31'])
        ->and($body['timeframe'])->toBe('Custom')
        ->and($body['type'])->toBe('ActualCost');
});
