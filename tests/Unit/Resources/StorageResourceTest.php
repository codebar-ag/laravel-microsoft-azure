<?php

use CodebarAg\MicrosoftAzure\Data\Arm\BlobContainerData;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountKeysData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateBlobContainer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\GetStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountKeys;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\ListStorageAccountsByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\RegenerateStorageAccountKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\SetBlobManagementPolicy;
use CodebarAg\MicrosoftAzure\Resources\StorageAccountsResource;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;

function storageAccountFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1',
        'name' => 'sa1',
        'location' => 'westeurope',
        'kind' => 'StorageV2',
        'sku' => ['name' => 'Standard_LRS'],
        'properties' => [
            'provisioningState' => 'Succeeded',
            'primaryEndpoints' => ['blob' => 'https://sa1.blob.core.windows.net/'],
        ],
        'tags' => ['project' => 'test'],
    ];
}

it('lists storage accounts in a resource group', function (): void {
    $client = clientWithArmMock([
        ListStorageAccountsByResourceGroup::class => MockResponse::make(body: [
            'value' => [storageAccountFixture()],
        ]),
    ]);

    $accounts = (new StorageAccountsResource($client, 'sub-1', 'rg-test'))->list();

    expect($accounts)->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->and($accounts->first()?->name)->toBe('sa1')
        ->and($accounts->first()?->primaryBlobEndpoint)->toBe('https://sa1.blob.core.windows.net/');
});

it('gets a storage account and maps provisioning state', function (): void {
    $client = clientWithArmMock([
        GetStorageAccount::class => MockResponse::make(body: storageAccountFixture()),
    ]);

    $account = (new StorageAccountsResource($client, 'sub-1', 'rg-test'))->account('sa1')->get();

    expect($account)->toBeInstanceOf(StorageAccountData::class)
        ->and($account->skuName)->toBe('Standard_LRS')
        ->and($account->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('creates or updates a storage account', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateStorageAccount::class => MockResponse::make(body: storageAccountFixture()),
    ]);

    $account = (new StorageAccountsResource($client, 'sub-1', 'rg-test'))->createOrUpdate('sa1', 'westeurope');

    expect($account->name)->toBe('sa1')
        ->and($account->provisioningState)->toBe(ProvisioningState::Succeeded);
});

it('lists storage account keys', function (): void {
    $client = clientWithArmMock([
        ListStorageAccountKeys::class => MockResponse::make(body: [
            'keys' => [
                ['keyName' => 'key1', 'value' => 'secret-1', 'permissions' => 'FULL'],
                ['keyName' => 'key2', 'value' => 'secret-2', 'permissions' => 'FULL'],
            ],
        ]),
    ]);

    $keys = (new StorageAccountsResource($client, 'sub-1', 'rg-test'))->account('sa1')->listKeys();

    expect($keys)->toBeInstanceOf(StorageAccountKeysData::class)
        ->and($keys->keys)->toHaveCount(2)
        ->and($keys->keys[0])->toBe(['keyName' => 'key1', 'value' => 'secret-1']);
});

it('regenerates a storage account key', function (): void {
    $client = clientWithArmMock([
        RegenerateStorageAccountKey::class => MockResponse::make(body: [
            'keys' => [
                ['keyName' => 'key1', 'value' => 'rotated-1'],
            ],
        ]),
    ]);

    $keys = (new StorageAccountsResource($client, 'sub-1', 'rg-test'))->account('sa1')->regenerateKey('key1');

    expect($keys->keys[0]['value'])->toBe('rotated-1');
});

it('creates or updates a blob container', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateBlobContainer::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/blobServices/default/containers/logs',
            'name' => 'logs',
            'properties' => ['publicAccess' => 'None'],
        ]),
    ]);

    $container = (new StorageAccountsResource($client, 'sub-1', 'rg-test'))
        ->account('sa1')
        ->blobContainers()
        ->createOrUpdate('logs');

    expect($container)->toBeInstanceOf(BlobContainerData::class)
        ->and($container->name)->toBe('logs')
        ->and($container->publicAccess)->toBe('None');
});

it('sets a blob management policy', function (): void {
    $client = clientWithArmMock([
        SetBlobManagementPolicy::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/managementPolicies/default',
            'name' => 'DefaultManagementPolicy',
        ]),
    ]);

    (new StorageAccountsResource($client, 'sub-1', 'rg-test'))
        ->account('sa1')
        ->blobContainers()
        ->setManagementPolicy([['enabled' => true, 'name' => 'expire']]);
})->throwsNoExceptions();
