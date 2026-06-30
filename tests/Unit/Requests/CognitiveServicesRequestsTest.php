<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CognitiveServicesAccountPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\ModelDeploymentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateKeyPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\CreateOrUpdateCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\DeleteCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\GetCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountModels;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountsByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\ListCognitiveServicesAccountSkus;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\RegenerateCognitiveServicesAccountKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\UpdateCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\CreateOrUpdateModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\DeleteModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\GetModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeployments;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeploymentSkus;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\CreateOrUpdateFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\DeleteFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\ListFoundryProjects;
use Saloon\Http\Request;

dataset('cognitive services request endpoints', [
    'ListCognitiveServicesAccountsByResourceGroup' => [
        fn () => new ListCognitiveServicesAccountsByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListCognitiveServicesAccounts' => [
        fn () => new ListCognitiveServicesAccounts('sub-1'),
        '/subscriptions/sub-1/providers/Microsoft.CognitiveServices/accounts',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'GetCognitiveServicesAccount' => [
        fn () => new GetCognitiveServicesAccount('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'DeleteCognitiveServicesAccount' => [
        fn () => new DeleteCognitiveServicesAccount('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListCognitiveServicesAccountKeys' => [
        fn () => new ListCognitiveServicesAccountKeys('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/listKeys',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListCognitiveServicesAccountModels' => [
        fn () => new ListCognitiveServicesAccountModels('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/models',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListCognitiveServicesAccountSkus' => [
        fn () => new ListCognitiveServicesAccountSkus('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/skus',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListModelDeployments' => [
        fn () => new ListModelDeployments('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/deployments',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'GetModelDeployment' => [
        fn () => new GetModelDeployment('sub-1', 'rg-test', 'aif-test', 'gpt-4o'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/deployments/gpt-4o',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'DeleteModelDeployment' => [
        fn () => new DeleteModelDeployment('sub-1', 'rg-test', 'aif-test', 'gpt-4o'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/deployments/gpt-4o',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListModelDeploymentSkus' => [
        fn () => new ListModelDeploymentSkus('sub-1', 'rg-test', 'aif-test', 'gpt-4o'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/deployments/gpt-4o/skus',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'ListFoundryProjects' => [
        fn () => new ListFoundryProjects('sub-1', 'rg-test', 'aif-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/projects',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'GetFoundryProject' => [
        fn () => new GetFoundryProject('sub-1', 'rg-test', 'aif-test', 'proj-1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/projects/proj-1',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
    'DeleteFoundryProject' => [
        fn () => new DeleteFoundryProject('sub-1', 'rg-test', 'aif-test', 'proj-1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/aif-test/projects/proj-1',
        ApiVersion::ARM_COGNITIVE_SERVICES,
    ],
]);

it('resolves cognitive services request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('cognitive services request endpoints');

it('builds cognitive services account body', function (): void {
    $request = new CreateOrUpdateCognitiveServicesAccount(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        accountName: 'aif-test',
        payload: new CognitiveServicesAccountPayload(
            location: 'westeurope',
            kind: 'AIServices',
            skuName: 'S0',
            properties: ['customSubDomainName' => 'aif-test'],
            tags: ['env' => 'test'],
        ),
    );

    expect($request->body()->all())
        ->toMatchArray([
            'location' => 'westeurope',
            'kind' => 'AIServices',
            'sku' => ['name' => 'S0'],
            'properties' => ['customSubDomainName' => 'aif-test'],
            'tags' => ['env' => 'test'],
        ]);
});

it('builds regenerate key body', function (): void {
    $request = new RegenerateCognitiveServicesAccountKey(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        accountName: 'aif-test',
        payload: new RegenerateKeyPayload('key1'),
    );

    expect($request->body()->all())->toBe(['keyName' => 'key1']);
});

it('builds model deployment body', function (): void {
    $request = new CreateOrUpdateModelDeployment(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        accountName: 'aif-test',
        deploymentName: 'gpt-4o',
        payload: new ModelDeploymentPayload('OpenAI', 'gpt-4o', '2024-08-06'),
    );

    expect($request->body()->all())
        ->toHaveKey('properties.model.format', 'OpenAI')
        ->toHaveKey('properties.model.name', 'gpt-4o');
});

it('builds foundry project body', function (): void {
    $request = new CreateOrUpdateFoundryProject(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        accountName: 'aif-test',
        projectName: 'proj-1',
        payload: new FoundryProjectPayload('westeurope', ['displayName' => 'Project 1']),
    );

    expect($request->body()->all())
        ->toMatchArray([
            'location' => 'westeurope',
            'properties' => ['displayName' => 'Project 1'],
        ]);
});

it('builds update cognitive services account body', function (): void {
    $request = new UpdateCognitiveServicesAccount(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        accountName: 'aif-test',
        payload: new CognitiveServicesAccountPayload(location: 'westeurope'),
    );

    expect($request->body()->all())->toHaveKey('location', 'westeurope');
});
