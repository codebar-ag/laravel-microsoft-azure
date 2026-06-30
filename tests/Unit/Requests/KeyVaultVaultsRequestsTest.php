<?php

use CodebarAg\MicrosoftAzure\Data\Payload\KeyVaultPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\CreateOrUpdateVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\DeleteVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\GetVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\ListVaultsByResourceGroup;
use Saloon\Http\Request;

dataset('key vault vault request endpoints', [
    'GetVault' => [
        fn () => new GetVault('sub-1', 'rg-test', 'kv1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.KeyVault/vaults/kv1',
        ApiVersion::ARM_KEY_VAULT_VAULTS,
    ],
    'ListVaultsByResourceGroup' => [
        fn () => new ListVaultsByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.KeyVault/vaults',
        ApiVersion::ARM_KEY_VAULT_VAULTS,
    ],
    'CreateOrUpdateVault' => [
        fn () => new CreateOrUpdateVault('sub-1', 'rg-test', 'kv1', new KeyVaultPayload('westeurope', 'tenant-1')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.KeyVault/vaults/kv1',
        ApiVersion::ARM_KEY_VAULT_VAULTS,
    ],
    'DeleteVault' => [
        fn () => new DeleteVault('sub-1', 'rg-test', 'kv1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.KeyVault/vaults/kv1',
        ApiVersion::ARM_KEY_VAULT_VAULTS,
    ],
]);

it('resolves key vault vault request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('key vault vault request endpoints');

it('builds create vault body with default properties', function (): void {
    $request = new CreateOrUpdateVault('sub-1', 'rg-test', 'kv1', new KeyVaultPayload('westeurope', 'tenant-1'));

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'tenantId' => 'tenant-1',
            'sku' => ['family' => 'A', 'name' => 'standard'],
            'enableRbacAuthorization' => true,
        ],
    ]);
});

it('builds create vault body with purge protection and tags', function (): void {
    $request = new CreateOrUpdateVault(
        'sub-1',
        'rg-test',
        'kv1',
        new KeyVaultPayload('westeurope', 'tenant-1', 'premium', false, true, [], ['project' => 'test']),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'properties' => [
            'tenantId' => 'tenant-1',
            'sku' => ['family' => 'A', 'name' => 'premium'],
            'enableRbacAuthorization' => false,
            'enablePurgeProtection' => true,
        ],
        'tags' => ['project' => 'test'],
    ]);
});
