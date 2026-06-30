<?php

use CodebarAg\MicrosoftAzure\Data\Arm\LogAnalyticsWorkspaceData;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\CreateOrUpdateWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\DeleteWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\GetWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\ListWorkspacesByResourceGroup;
use CodebarAg\MicrosoftAzure\Resources\LogAnalyticsWorkspacesResource;
use Saloon\Http\Faking\MockResponse;

function logAnalyticsWorkspaceFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces/law1',
        'name' => 'law1',
        'location' => 'westeurope',
        'properties' => [
            'customerId' => '00000000-0000-0000-0000-0000000000aa',
            'provisioningState' => 'Succeeded',
            'sku' => ['name' => 'PerGB2018'],
            'retentionInDays' => 90,
        ],
    ];
}

it('lists log analytics workspaces via resource gateway', function (): void {
    $client = clientWithArmMock([
        ListWorkspacesByResourceGroup::class => MockResponse::make(body: ['value' => [logAnalyticsWorkspaceFixture()]]),
    ]);

    $workspaces = (new LogAnalyticsWorkspacesResource($client, 'sub-1', 'rg-test'))->list();

    expect($workspaces)->toHaveCount(1)
        ->and($workspaces->first())->toBeInstanceOf(LogAnalyticsWorkspaceData::class)
        ->and($workspaces->first()?->name)->toBe('law1')
        ->and($workspaces->first()?->customerId)->toBe('00000000-0000-0000-0000-0000000000aa')
        ->and($workspaces->first()?->skuName)->toBe('PerGB2018')
        ->and($workspaces->first()?->retentionInDays)->toBe(90);
});

it('gets a log analytics workspace via workspace resource gateway', function (): void {
    $client = clientWithArmMock([
        GetWorkspace::class => MockResponse::make(body: logAnalyticsWorkspaceFixture()),
    ]);

    $workspace = (new LogAnalyticsWorkspacesResource($client, 'sub-1', 'rg-test'))->workspace('law1')->get();

    expect($workspace)->toBeInstanceOf(LogAnalyticsWorkspaceData::class)
        ->and($workspace->provisioningState)->toBe('Succeeded');
});

it('creates or updates a log analytics workspace via resource gateway', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateWorkspace::class => MockResponse::make(body: logAnalyticsWorkspaceFixture()),
    ]);

    $workspace = (new LogAnalyticsWorkspacesResource($client, 'sub-1', 'rg-test'))
        ->createOrUpdate('law1', 'westeurope', 'PerGB2018', 90);

    expect($workspace)->toBeInstanceOf(LogAnalyticsWorkspaceData::class)
        ->and($workspace->name)->toBe('law1');
});

it('deletes a log analytics workspace via workspace resource gateway', function (): void {
    $client = clientWithArmMock([
        DeleteWorkspace::class => MockResponse::make(body: '', status: 200),
    ]);

    (new LogAnalyticsWorkspacesResource($client, 'sub-1', 'rg-test'))->workspace('law1')->delete();
})->throwsNoExceptions();
