<?php

use CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsWorkspacePayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\CreateOrUpdateWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\DeleteWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\GetWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\ListWorkspacesByResourceGroup;
use Saloon\Http\Request;

dataset('log analytics request endpoints', [
    'GetWorkspace' => [
        fn () => new GetWorkspace('sub-1', 'rg-test', 'law1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces/law1',
        ApiVersion::ARM_LOG_ANALYTICS,
    ],
    'ListWorkspacesByResourceGroup' => [
        fn () => new ListWorkspacesByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces',
        ApiVersion::ARM_LOG_ANALYTICS,
    ],
    'CreateOrUpdateWorkspace' => [
        fn () => new CreateOrUpdateWorkspace('sub-1', 'rg-test', 'law1', new LogAnalyticsWorkspacePayload('westeurope')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces/law1',
        ApiVersion::ARM_LOG_ANALYTICS,
    ],
    'DeleteWorkspace' => [
        fn () => new DeleteWorkspace('sub-1', 'rg-test', 'law1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces/law1',
        ApiVersion::ARM_LOG_ANALYTICS,
    ],
]);

it('resolves log analytics request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('log analytics request endpoints');

it('builds log analytics workspace body with sku and retention', function (): void {
    $request = new CreateOrUpdateWorkspace(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        workspaceName: 'law1',
        payload: new LogAnalyticsWorkspacePayload('westeurope', 'PerGB2018', 90, ['publicNetworkAccessForIngestion' => 'Enabled'], ['env' => 'prod']),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'sku' => ['name' => 'PerGB2018'],
            'retentionInDays' => 90,
            'publicNetworkAccessForIngestion' => 'Enabled',
        ],
        'tags' => ['env' => 'prod'],
    ]);
});

it('builds log analytics workspace body with defaults and no tags', function (): void {
    $request = new CreateOrUpdateWorkspace(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        workspaceName: 'law1',
        payload: new LogAnalyticsWorkspacePayload('westeurope'),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'sku' => ['name' => 'PerGB2018'],
            'retentionInDays' => 30,
        ],
    ]);
});
