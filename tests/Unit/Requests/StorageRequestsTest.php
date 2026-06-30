<?php

use CodebarAg\MicrosoftAzure\Data\Payload\BlobContainerPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\BlobManagementPolicyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RegenerateStorageKeyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\StorageAccountPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateBlobContainer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\DeleteStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\GetStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountsByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\RegenerateStorageAccountKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\SetBlobManagementPolicy;
use Saloon\Http\Request;

dataset('storage request endpoints', [
    'GetStorageAccount' => [
        fn () => new GetStorageAccount('sub-1', 'rg-test', 'sa1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1',
        ApiVersion::ARM_STORAGE,
    ],
    'ListStorageAccountsByResourceGroup' => [
        fn () => new ListStorageAccountsByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts',
        ApiVersion::ARM_STORAGE,
    ],
    'CreateOrUpdateStorageAccount' => [
        fn () => new CreateOrUpdateStorageAccount('sub-1', 'rg-test', 'sa1', new StorageAccountPayload('westeurope')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1',
        ApiVersion::ARM_STORAGE,
    ],
    'DeleteStorageAccount' => [
        fn () => new DeleteStorageAccount('sub-1', 'rg-test', 'sa1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1',
        ApiVersion::ARM_STORAGE,
    ],
    'ListStorageAccountKeys' => [
        fn () => new ListStorageAccountKeys('sub-1', 'rg-test', 'sa1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/listKeys',
        ApiVersion::ARM_STORAGE,
    ],
    'RegenerateStorageAccountKey' => [
        fn () => new RegenerateStorageAccountKey('sub-1', 'rg-test', 'sa1', new RegenerateStorageKeyPayload('key1')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/regenerateKey',
        ApiVersion::ARM_STORAGE,
    ],
    'CreateOrUpdateBlobContainer' => [
        fn () => new CreateOrUpdateBlobContainer('sub-1', 'rg-test', 'sa1', 'logs', new BlobContainerPayload),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/blobServices/default/containers/logs',
        ApiVersion::ARM_STORAGE,
    ],
    'SetBlobManagementPolicy' => [
        fn () => new SetBlobManagementPolicy('sub-1', 'rg-test', 'sa1', new BlobManagementPolicyPayload([])),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/managementPolicies/default',
        ApiVersion::ARM_STORAGE,
    ],
]);

it('resolves storage request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('storage request endpoints');

it('builds create storage account body with sku, kind, location and tags', function (): void {
    $request = new CreateOrUpdateStorageAccount(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        accountName: 'sa1',
        payload: new StorageAccountPayload('westeurope', 'Standard_LRS', 'StorageV2', [], ['project' => 'test']),
    );

    expect($request->body()->all())->toBe([
        'sku' => ['name' => 'Standard_LRS'],
        'kind' => 'StorageV2',
        'location' => 'westeurope',
        'properties' => [],
        'tags' => ['project' => 'test'],
    ]);
});

it('builds regenerate storage key body', function (): void {
    $request = new RegenerateStorageAccountKey('sub-1', 'rg-test', 'sa1', new RegenerateStorageKeyPayload('key2'));

    expect($request->body()->all())->toBe(['keyName' => 'key2']);
});

it('builds blob container body with public access', function (): void {
    $request = new CreateOrUpdateBlobContainer('sub-1', 'rg-test', 'sa1', 'logs', new BlobContainerPayload('Blob'));

    expect($request->body()->all())->toBe([
        'properties' => ['publicAccess' => 'Blob'],
    ]);
});

it('builds blob management policy body with rules', function (): void {
    $rules = [['enabled' => true, 'name' => 'expire']];
    $request = new SetBlobManagementPolicy('sub-1', 'rg-test', 'sa1', new BlobManagementPolicyPayload($rules));

    expect($request->body()->all())->toBe([
        'properties' => ['policy' => ['rules' => $rules]],
    ]);
});
