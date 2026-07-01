<?php

use CodebarAg\MicrosoftAzure\Data\Payload\ResourceGroupPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RoleAssignmentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\SqlFirewallRulePayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\PurgeDeletedVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CancelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\ListDeploymentOperations;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\CreateOrUpdateResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\DeleteResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\ListResourceGroups;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\GetResourceProvider;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\ListResourceProviders;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\RegisterResourceProvider;
use CodebarAg\MicrosoftAzure\Requests\Arm\RoleAssignments\CreateRoleAssignment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\DeleteSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\ListSubscriptionAliases;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\GetSubscription;
use Saloon\Http\Request;

dataset('arm request endpoints', [
    'ListResourceGroups' => [
        fn () => new ListResourceGroups('sub-1'),
        '/subscriptions/sub-1/resourcegroups',
        ApiVersion::ARM_RESOURCES,
    ],
    'GetResourceGroup' => [
        fn () => new GetResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourcegroups/rg-test',
        ApiVersion::ARM_RESOURCES,
    ],
    'DeleteResourceGroup' => [
        fn () => new DeleteResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourcegroups/rg-test',
        ApiVersion::ARM_RESOURCES,
    ],
    'GetDeployment' => [
        fn () => new GetDeployment('sub-1', 'rg-test', 'tenantflow'),
        '/subscriptions/sub-1/resourcegroups/rg-test/providers/Microsoft.Resources/deployments/tenantflow',
        ApiVersion::ARM_DEPLOYMENTS,
    ],
    'ListDeploymentOperations' => [
        fn () => new ListDeploymentOperations('sub-1', 'rg-test', 'tenantflow'),
        '/subscriptions/sub-1/resourcegroups/rg-test/providers/Microsoft.Resources/deployments/tenantflow/operations',
        ApiVersion::ARM_DEPLOYMENTS,
    ],
    'CancelDeployment' => [
        fn () => new CancelDeployment('sub-1', 'rg-test', 'tenantflow'),
        '/subscriptions/sub-1/resourcegroups/rg-test/providers/Microsoft.Resources/deployments/tenantflow/cancel',
        ApiVersion::ARM_DEPLOYMENTS,
    ],
    'GetSubscription' => [
        fn () => new GetSubscription('sub-1'),
        '/subscriptions/sub-1',
        ApiVersion::ARM_SUBSCRIPTIONS,
    ],
    'ListSubscriptionAliases' => [
        fn () => new ListSubscriptionAliases,
        '/providers/Microsoft.Subscription/aliases',
        ApiVersion::ARM_SUBSCRIPTION_ALIASES,
    ],
    'GetSubscriptionAlias' => [
        fn () => new GetSubscriptionAlias('tenant-acme'),
        '/providers/Microsoft.Subscription/aliases/tenant-acme',
        ApiVersion::ARM_SUBSCRIPTION_ALIASES,
    ],
    'DeleteSubscriptionAlias' => [
        fn () => new DeleteSubscriptionAlias('tenant-acme'),
        '/providers/Microsoft.Subscription/aliases/tenant-acme',
        ApiVersion::ARM_SUBSCRIPTION_ALIASES,
    ],
    'ListDeletedVaults' => [
        fn () => new ListDeletedVaults('sub-1', 'westeurope'),
        '/subscriptions/sub-1/providers/Microsoft.KeyVault/locations/westeurope/deletedVaults',
        ApiVersion::ARM_DELETED_VAULTS,
    ],
    'PurgeDeletedVault' => [
        fn () => new PurgeDeletedVault('sub-1', 'westeurope', 'kv-test'),
        '/subscriptions/sub-1/providers/Microsoft.KeyVault/locations/westeurope/deletedVaults/kv-test/purge',
        ApiVersion::ARM_DELETED_VAULTS,
    ],
    'ListDeletedCognitiveServicesAccounts' => [
        fn () => new ListDeletedCognitiveServicesAccounts('sub-1', 'westeurope'),
        '/subscriptions/sub-1/providers/Microsoft.CognitiveServices/locations/westeurope/deletedAccounts',
        ApiVersion::ARM_DELETED_COGNITIVE_SERVICES,
    ],
    'PurgeDeletedCognitiveServicesAccount' => [
        fn () => new PurgeDeletedCognitiveServicesAccount('sub-1', 'westeurope', 'aif-test'),
        '/subscriptions/sub-1/providers/Microsoft.CognitiveServices/locations/westeurope/deletedAccounts/aif-test/purge',
        ApiVersion::ARM_DELETED_COGNITIVE_SERVICES,
    ],
    'GetSqlDatabase' => [
        fn () => new GetSqlDatabase('sub-1', 'rg-test', 'sql1', 'datalogs'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/databases/datalogs',
        ApiVersion::ARM_SQL,
    ],
    'DeleteSqlFirewallRule' => [
        fn () => new DeleteSqlFirewallRule('sub-1', 'rg-test', 'sql1', 'deployer-migrate'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Sql/servers/sql1/firewallRules/deployer-migrate',
        ApiVersion::ARM_SQL,
    ],
    'GetResourceProvider' => [
        fn () => new GetResourceProvider('sub-1', 'Microsoft.Storage'),
        '/subscriptions/sub-1/providers/Microsoft.Storage',
        ApiVersion::ARM_RESOURCE_PROVIDERS,
    ],
    'RegisterResourceProvider' => [
        fn () => new RegisterResourceProvider('sub-1', 'Microsoft.Storage'),
        '/subscriptions/sub-1/providers/Microsoft.Storage/register',
        ApiVersion::ARM_RESOURCE_PROVIDERS,
    ],
    'ListResourceProviders' => [
        fn () => new ListResourceProviders('sub-1'),
        '/subscriptions/sub-1/providers',
        ApiVersion::ARM_RESOURCE_PROVIDERS,
    ],
]);

it('resolves ARM request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('arm request endpoints');

it('builds create resource group body with location and tags', function (): void {
    $request = new CreateOrUpdateResourceGroup(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        payload: new ResourceGroupPayload(
            location: 'westeurope',
            tags: ['project' => 'test'],
        ),
    );

    expect($request->body()->all())
        ->toMatchArray([
            'location' => 'westeurope',
            'tags' => ['project' => 'test'],
        ]);
});

it('builds role assignment body with optional principal type', function (): void {
    $request = new CreateRoleAssignment(
        scope: 'subscriptions/sub-1/resourceGroups/rg-test',
        roleAssignmentName: '00000000-0000-0000-0000-000000000001',
        payload: new RoleAssignmentPayload(
            roleDefinitionId: '/subscriptions/sub-1/providers/Microsoft.Authorization/roleDefinitions/123',
            principalId: '00000000-0000-0000-0000-000000000010',
            principalType: 'ServicePrincipal',
        ),
    );

    expect($request->body()->all())
        ->toHaveKey('properties.roleDefinitionId')
        ->toHaveKey('properties.principalType', 'ServicePrincipal');
});

it('builds sql firewall rule body with ip range', function (): void {
    $request = new CreateOrUpdateSqlFirewallRule(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serverName: 'sql1',
        ruleName: 'deployer-migrate',
        payload: new SqlFirewallRulePayload('1.2.3.4', '1.2.3.4'),
    );

    expect($request->body()->all())
        ->toBe([
            'properties' => [
                'startIpAddress' => '1.2.3.4',
                'endIpAddress' => '1.2.3.4',
            ],
        ]);
});

it('prefixes role assignment scope with a leading slash when missing', function (): void {
    $request = new CreateRoleAssignment(
        scope: 'subscriptions/sub-1',
        roleAssignmentName: 'guid',
        payload: new RoleAssignmentPayload(
            roleDefinitionId: '/roleDefinitions/123',
            principalId: 'principal',
        ),
    );

    expect($request->resolveEndpoint())
        ->toBe('/subscriptions/sub-1/providers/Microsoft.Authorization/roleAssignments/guid');
});
