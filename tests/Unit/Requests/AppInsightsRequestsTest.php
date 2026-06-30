<?php

use CodebarAg\MicrosoftAzure\Data\Payload\ApplicationInsightsComponentPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\CreateOrUpdateComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\DeleteComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\GetComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\ListComponentsByResourceGroup;
use Saloon\Http\Request;

dataset('app insights request endpoints', [
    'GetComponent' => [
        fn () => new GetComponent('sub-1', 'rg-test', 'ai1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Insights/components/ai1',
        ApiVersion::ARM_APP_INSIGHTS,
    ],
    'ListComponentsByResourceGroup' => [
        fn () => new ListComponentsByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Insights/components',
        ApiVersion::ARM_APP_INSIGHTS,
    ],
    'CreateOrUpdateComponent' => [
        fn () => new CreateOrUpdateComponent('sub-1', 'rg-test', 'ai1', new ApplicationInsightsComponentPayload('westeurope')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Insights/components/ai1',
        ApiVersion::ARM_APP_INSIGHTS,
    ],
    'DeleteComponent' => [
        fn () => new DeleteComponent('sub-1', 'rg-test', 'ai1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Insights/components/ai1',
        ApiVersion::ARM_APP_INSIGHTS,
    ],
]);

it('resolves app insights request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('app insights request endpoints');

it('builds app insights component body with workspace resource id', function (): void {
    $request = new CreateOrUpdateComponent(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        componentName: 'ai1',
        payload: new ApplicationInsightsComponentPayload('westeurope', 'web', 'web', '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces/law1', ['Flow_Type' => 'Bluefield'], ['env' => 'prod']),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'kind' => 'web',
        'properties' => [
            'Application_Type' => 'web',
            'WorkspaceResourceId' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.OperationalInsights/workspaces/law1',
            'Flow_Type' => 'Bluefield',
        ],
        'tags' => ['env' => 'prod'],
    ]);
});

it('omits workspace resource id from app insights body when not provided', function (): void {
    $request = new CreateOrUpdateComponent(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        componentName: 'ai1',
        payload: new ApplicationInsightsComponentPayload('westeurope'),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'kind' => 'web',
        'properties' => ['Application_Type' => 'web'],
    ]);
});
