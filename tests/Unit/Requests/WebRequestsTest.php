<?php

use CodebarAg\MicrosoftAzure\Data\Payload\AppSettingsPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\ServerFarmPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\WebSitePayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\GetFunction;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\ListFunctions;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateFunctionKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\CreateOrUpdateHostKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteFunctionKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\DeleteHostKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListFunctionKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys\ListHostKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\ServerFarms\CreateOrUpdateServerFarm;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\ServerFarms\GetServerFarm;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListApplicationSettings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\ListConnectionStrings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings\UpdateApplicationSettings;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\DeleteSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSiteConfig;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\ListSitesByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\RestartSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StartSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StopSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\UpdateBasicPublishingCredentialsPolicy;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\ListSyncFunctionTriggersStatus;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\SyncFunctionTriggers;
use Saloon\Http\Request;

$webBase = '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/my-func';

dataset('web request endpoints', [
    'ListSitesByResourceGroup' => [
        fn () => new ListSitesByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites',
        ApiVersion::ARM_WEB,
    ],
    'GetSite' => [
        fn () => new GetSite('sub-1', 'rg-test', 'my-func'),
        $webBase,
        ApiVersion::ARM_WEB,
    ],
    'DeleteSite' => [
        fn () => new DeleteSite('sub-1', 'rg-test', 'my-func'),
        $webBase,
        ApiVersion::ARM_WEB,
    ],
    'RestartSite' => [
        fn () => new RestartSite('sub-1', 'rg-test', 'my-func'),
        $webBase.'/restart',
        ApiVersion::ARM_WEB,
    ],
    'StartSite' => [
        fn () => new StartSite('sub-1', 'rg-test', 'my-func'),
        $webBase.'/start',
        ApiVersion::ARM_WEB,
    ],
    'StopSite' => [
        fn () => new StopSite('sub-1', 'rg-test', 'my-func'),
        $webBase.'/stop',
        ApiVersion::ARM_WEB,
    ],
    'GetSiteConfig' => [
        fn () => new GetSiteConfig('sub-1', 'rg-test', 'my-func'),
        $webBase.'/config/web',
        ApiVersion::ARM_WEB,
    ],
    'ListApplicationSettings' => [
        fn () => new ListApplicationSettings('sub-1', 'rg-test', 'my-func'),
        $webBase.'/config/appsettings/list',
        ApiVersion::ARM_WEB,
    ],
    'ListConnectionStrings' => [
        fn () => new ListConnectionStrings('sub-1', 'rg-test', 'my-func'),
        $webBase.'/config/connectionstrings/list',
        ApiVersion::ARM_WEB,
    ],
    'ListFunctions' => [
        fn () => new ListFunctions('sub-1', 'rg-test', 'my-func'),
        $webBase.'/functions',
        ApiVersion::ARM_WEB,
    ],
    'GetFunction' => [
        fn () => new GetFunction('sub-1', 'rg-test', 'my-func', 'HttpTrigger'),
        $webBase.'/functions/HttpTrigger',
        ApiVersion::ARM_WEB,
    ],
    'ListHostKeys' => [
        fn () => new ListHostKeys('sub-1', 'rg-test', 'my-func'),
        $webBase.'/host/default/listkeys',
        ApiVersion::ARM_WEB,
    ],
    'DeleteHostKey' => [
        fn () => new DeleteHostKey('sub-1', 'rg-test', 'my-func', 'default'),
        $webBase.'/host/default/keys/default',
        ApiVersion::ARM_WEB,
    ],
    'ListFunctionKeys' => [
        fn () => new ListFunctionKeys('sub-1', 'rg-test', 'my-func', 'HttpTrigger'),
        $webBase.'/functions/HttpTrigger/listkeys',
        ApiVersion::ARM_WEB,
    ],
    'DeleteFunctionKey' => [
        fn () => new DeleteFunctionKey('sub-1', 'rg-test', 'my-func', 'HttpTrigger', 'default'),
        $webBase.'/functions/HttpTrigger/keys/default',
        ApiVersion::ARM_WEB,
    ],
    'SyncFunctionTriggers' => [
        fn () => new SyncFunctionTriggers('sub-1', 'rg-test', 'my-func'),
        $webBase.'/syncfunctiontriggers',
        ApiVersion::ARM_WEB,
    ],
    'ListSyncFunctionTriggersStatus' => [
        fn () => new ListSyncFunctionTriggersStatus('sub-1', 'rg-test', 'my-func'),
        $webBase.'/syncfunctiontriggers/status',
        ApiVersion::ARM_WEB,
    ],
    'GetServerFarm' => [
        fn () => new GetServerFarm('sub-1', 'rg-test', 'my-plan'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/serverfarms/my-plan',
        ApiVersion::ARM_WEB,
    ],
    'CreateOrUpdateServerFarm' => [
        fn () => new CreateOrUpdateServerFarm('sub-1', 'rg-test', 'my-plan', new ServerFarmPayload('switzerlandnorth')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/serverfarms/my-plan',
        ApiVersion::ARM_WEB,
    ],
    'UpdateBasicPublishingCredentialsPolicy' => [
        fn () => new UpdateBasicPublishingCredentialsPolicy('sub-1', 'rg-test', 'my-func', 'scm', false),
        $webBase.'/basicPublishingCredentialsPolicies/scm',
        ApiVersion::ARM_WEB,
    ],
]);

it('resolves web request endpoints and api-version query', function (callable $factory, string $endpoint, ApiVersion $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion->value()]);
})->with('web request endpoints');

it('builds create function app body', function (): void {
    $request = new CreateOrUpdateSite(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        appName: 'my-func',
        payload: new WebSitePayload(
            location: 'westeurope',
            properties: ['serverFarmId' => '/plan/id'],
        ),
    );

    expect($request->body()->all())
        ->toMatchArray([
            'location' => 'westeurope',
            'kind' => 'functionapp',
            'properties' => ['serverFarmId' => '/plan/id'],
        ])
        ->and($request->body()->all())->not->toHaveKey('identity');
});

it('sets a system-assigned identity on the function app body when requested', function (): void {
    $request = new CreateOrUpdateSite(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        appName: 'my-func',
        payload: new WebSitePayload(
            location: 'westeurope',
            properties: ['serverFarmId' => '/plan/id'],
            identityType: 'SystemAssigned',
        ),
    );

    expect($request->body()->all())->toHaveKey('identity', ['type' => 'SystemAssigned']);
});

it('builds app settings body', function (): void {
    $request = new UpdateApplicationSettings(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        appName: 'my-func',
        payload: new AppSettingsPayload(['FUNCTIONS_WORKER_RUNTIME' => 'node']),
    );

    expect($request->body()->all())
        ->toBe(['properties' => ['FUNCTIONS_WORKER_RUNTIME' => 'node']]);
});

it('builds function key body', function (): void {
    $request = new CreateOrUpdateHostKey(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        appName: 'my-func',
        keyName: 'default',
        payload: new FunctionKeyPayload('secret-value'),
    );

    expect($request->body()->all())
        ->toBe(['properties' => ['value' => 'secret-value']]);

    $functionKeyRequest = new CreateOrUpdateFunctionKey(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        appName: 'my-func',
        functionName: 'HttpTrigger',
        keyName: 'default',
        payload: new FunctionKeyPayload('secret-value'),
    );

    expect($functionKeyRequest->resolveEndpoint())
        ->toBe('/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/my-func/functions/HttpTrigger/keys/default');
});

it('builds an app service plan body with sku, kind and tags', function (): void {
    $request = new CreateOrUpdateServerFarm(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        planName: 'my-plan',
        payload: new ServerFarmPayload('switzerlandnorth', 'Y1', 'Dynamic', 'linux', ['reserved' => true], ['project' => 'test']),
    );

    expect($request->body()->all())->toBe([
        'location' => 'switzerlandnorth',
        'sku' => ['name' => 'Y1', 'tier' => 'Dynamic'],
        'properties' => ['reserved' => true],
        'kind' => 'linux',
        'tags' => ['project' => 'test'],
    ]);
});

it('omits kind and tags from a plan body rather than defaulting an OS', function (): void {
    // ARM's own default is Windows, so emitting a kind nobody asked for picks
    // an OS on the caller's behalf.
    expect((new ServerFarmPayload('switzerlandnorth'))->toAzureBody())->toBe([
        'location' => 'switzerlandnorth',
        'sku' => ['name' => 'Y1', 'tier' => 'Dynamic'],
        'properties' => [],
    ]);
});

it('builds a publishing credentials policy body in both directions', function (): void {
    expect((new UpdateBasicPublishingCredentialsPolicy('s', 'rg', 'site', 'scm', false))->body()->all())
        ->toBe(['properties' => ['allow' => false]])
        ->and((new UpdateBasicPublishingCredentialsPolicy('s', 'rg', 'site', 'ftp', true))->body()->all())
        ->toBe(['properties' => ['allow' => true]]);
});
