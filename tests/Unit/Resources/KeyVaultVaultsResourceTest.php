<?php

use CodebarAg\MicrosoftAzure\Data\Arm\KeyVaultData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\CreateOrUpdateVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\GetVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\ListVaultsByResourceGroup;
use CodebarAg\MicrosoftAzure\Resources\KeyVaultsResource;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;

function armVaultFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.KeyVault/vaults/kv1',
        'name' => 'kv1',
        'location' => 'westeurope',
        'properties' => [
            'vaultUri' => 'https://kv1.vault.azure.net/',
            'provisioningState' => 'Succeeded',
        ],
        'tags' => ['project' => 'test'],
    ];
}

it('lists key vaults in a resource group', function (): void {
    $client = clientWithArmMock([
        ListVaultsByResourceGroup::class => MockResponse::make(body: [
            'value' => [armVaultFixture()],
        ]),
    ]);

    $vaults = (new KeyVaultsResource($client, 'sub-1', 'rg-test'))->list();

    expect($vaults)->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->and($vaults->first()?->name)->toBe('kv1')
        ->and($vaults->first()?->vaultUri)->toBe('https://kv1.vault.azure.net/');
});

it('gets an arm key vault', function (): void {
    $client = clientWithArmMock([
        GetVault::class => MockResponse::make(body: armVaultFixture()),
    ]);

    $vault = (new KeyVaultsResource($client, 'sub-1', 'rg-test'))->vault('kv1')->get();

    expect($vault)->toBeInstanceOf(KeyVaultData::class)
        ->and($vault->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('creates or updates an arm key vault', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateVault::class => MockResponse::make(body: armVaultFixture()),
    ]);

    $vault = (new KeyVaultsResource($client, 'sub-1', 'rg-test'))->createOrUpdate('kv1', 'westeurope', 'tenant-1');

    expect($vault->name)->toBe('kv1')
        ->and($vault->provisioningState)->toBe(ProvisioningState::Succeeded);
});
