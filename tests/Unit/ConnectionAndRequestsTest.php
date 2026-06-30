<?php

use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;

it('builds a stable connection identifier', function (): void {
    $a = ConnectionConfig::make('acme', [
        'tenantId' => 'tenant-a',
        'clientId' => 'client-a',
        'clientSecret' => 'secret-a',
        'subscriptionId' => 'sub-a',
    ]);

    $b = ConnectionConfig::make('other', [
        'tenantId' => 'tenant-a',
        'clientId' => 'client-a',
        'clientSecret' => 'secret-a',
        'subscriptionId' => 'sub-a',
    ]);

    expect($a->identifier())->not->toBe($b->identifier());
});

it('maps token audiences to OAuth scopes', function (): void {
    expect(TokenAudience::Arm->scope())->toBe('https://management.azure.com/.default')
        ->and(TokenAudience::KeyVault->scope())->toBe('https://vault.azure.net/.default')
        ->and(TokenAudience::Graph->scope())->toBe('https://graph.microsoft.com/.default');
});

it('knows terminal provisioning states', function (): void {
    expect(ProvisioningState::Succeeded->isTerminal())->toBeTrue()
        ->and(ProvisioningState::Failed->isTerminal())->toBeTrue()
        ->and(ProvisioningState::Running->isTerminal())->toBeFalse();
});

it('resolves ARM resource group endpoint', function (): void {
    $request = new GetResourceGroup('sub-1', 'rg-test');

    expect($request->resolveEndpoint())
        ->toBe('/subscriptions/sub-1/resourcegroups/rg-test');
});

it('builds deployment request body', function (): void {
    $request = new CreateOrUpdateDeployment(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        deploymentName: 'tenantflow',
        template: ['$schema' => 'https://schema.management.azure.com/schemas/2019-04-01/deploymentTemplate.json#'],
        parameters: ['location' => ['value' => 'westeurope']],
    );

    expect($request->body()->all())
        ->toHaveKey('properties.mode', 'Incremental')
        ->toHaveKey('properties.template');
});
