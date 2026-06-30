<?php

use CodebarAg\MicrosoftAzure\Data\Arm\ApplicationInsightsComponentData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\CreateOrUpdateComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\DeleteComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\GetComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\ListComponentsByResourceGroup;
use CodebarAg\MicrosoftAzure\Resources\ApplicationInsightsResource;
use Saloon\Http\Faking\MockResponse;

function appInsightsComponentFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Insights/components/ai1',
        'name' => 'ai1',
        'location' => 'westeurope',
        'properties' => [
            'InstrumentationKey' => '11111111-2222-3333-4444-555555555555',
            'ConnectionString' => 'InstrumentationKey=11111111-2222-3333-4444-555555555555',
            'AppId' => '99999999-8888-7777-6666-555555555555',
            'provisioningState' => 'Succeeded',
        ],
    ];
}

it('lists application insights components via resource gateway', function (): void {
    $client = clientWithArmMock([
        ListComponentsByResourceGroup::class => MockResponse::make(body: ['value' => [appInsightsComponentFixture()]]),
    ]);

    $components = (new ApplicationInsightsResource($client, 'sub-1', 'rg-test'))->list();

    expect($components)->toHaveCount(1)
        ->and($components->first())->toBeInstanceOf(ApplicationInsightsComponentData::class)
        ->and($components->first()?->name)->toBe('ai1')
        ->and($components->first()?->instrumentationKey)->toBe('11111111-2222-3333-4444-555555555555')
        ->and($components->first()?->connectionString)->toBe('InstrumentationKey=11111111-2222-3333-4444-555555555555')
        ->and($components->first()?->appId)->toBe('99999999-8888-7777-6666-555555555555')
        ->and($components->first()?->provisioningState)->toBe('Succeeded');
});

it('gets an application insights component via component resource gateway', function (): void {
    $client = clientWithArmMock([
        GetComponent::class => MockResponse::make(body: appInsightsComponentFixture()),
    ]);

    $component = (new ApplicationInsightsResource($client, 'sub-1', 'rg-test'))->component('ai1')->get();

    expect($component)->toBeInstanceOf(ApplicationInsightsComponentData::class)
        ->and($component->name)->toBe('ai1');
});

it('creates or updates an application insights component via resource gateway', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateComponent::class => MockResponse::make(body: appInsightsComponentFixture()),
    ]);

    $component = (new ApplicationInsightsResource($client, 'sub-1', 'rg-test'))
        ->createOrUpdate('ai1', 'westeurope');

    expect($component)->toBeInstanceOf(ApplicationInsightsComponentData::class)
        ->and($component->name)->toBe('ai1');
});

it('deletes an application insights component via component resource gateway', function (): void {
    $client = clientWithArmMock([
        DeleteComponent::class => MockResponse::make(body: '', status: 200),
    ]);

    (new ApplicationInsightsResource($client, 'sub-1', 'rg-test'))->component('ai1')->delete();
})->throwsNoExceptions();
