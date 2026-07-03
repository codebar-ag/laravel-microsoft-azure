<?php

use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Data\KeyVault\SecretData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Tests\Support\MicrosoftAzureFixture;
use Saloon\Http\Faking\MockResponse;

it('deserializes resource group data from Azure JSON', function (): void {
    $data = ResourceGroupData::fromAzure(resourceGroupFixture());

    expect($data->name)->toBe('rg-test')
        ->and($data->location)->toBe('westeurope')
        ->and($data->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('deserializes deployment data from Azure JSON', function (): void {
    $data = DeploymentData::fromAzure(deploymentFixture());

    expect($data->name)->toBe('tenantflow')
        ->and($data->provisioningState)->toBe(ProvisioningState::Running);
});

it('deserializes key vault secret data', function (): void {
    $data = SecretData::fromAzure(secretFixture());

    expect($data->name)->toBe('webhook-token')
        ->and($data->value)->toBe('secret-value');
});

it('replays a committed saloon fixture for get resource group', function (): void {
    $client = clientWithMock([
        GetResourceGroup::class => new MicrosoftAzureFixture('get-resource-group'),
    ]);

    $group = $client->resourceGroups('sub-1')->get('rg-test');

    expect($group->name)->toBe('rg-test')
        ->and($group->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('uses inline mock responses without network for ARM calls', function (): void {
    $client = clientWithMock([
        GetResourceGroup::class => MockResponse::make(body: resourceGroupFixture()),
    ]);

    $group = $client->resourceGroups('sub-1')->get('rg-test');

    expect($group->name)->toBe('rg-test');
});
