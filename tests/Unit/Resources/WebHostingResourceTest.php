<?php

use CodebarAg\MicrosoftAzure\Data\Arm\BasicPublishingCredentialsPolicyData;
use CodebarAg\MicrosoftAzure\Data\Arm\ServerFarmData;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageTableData;
use CodebarAg\MicrosoftAzure\Enums\ObservedState;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\CreateOrUpdateStorageTable;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\ServerFarms\CreateOrUpdateServerFarm;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\ServerFarms\GetServerFarm;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\UpdateBasicPublishingCredentialsPolicy;
use CodebarAg\MicrosoftAzure\Resources\ServerFarmsResource;
use Saloon\Http\Faking\MockResponse;

/**
 * @return array<string, mixed>
 */
function serverFarmFixture(string $provisioningState = 'Succeeded'): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/serverfarms/my-plan',
        'name' => 'my-plan',
        'location' => 'Switzerland North',
        'kind' => 'linux',
        'sku' => ['name' => 'Y1', 'tier' => 'Dynamic'],
        'properties' => ['reserved' => true, 'status' => 'Ready', 'provisioningState' => $provisioningState],
        'tags' => ['project' => 'test'],
    ];
}

it('exposes the server farm gateway through the client', function (): void {
    expect(clientWithSeededToken()->serverFarms('sub-1', 'rg-test'))
        ->toBeInstanceOf(ServerFarmsResource::class);
});

it('gets an app service plan and maps sku, kind and lifecycle', function (): void {
    $client = clientWithArmMock([
        GetServerFarm::class => MockResponse::make(body: serverFarmFixture()),
    ]);

    $plan = $client->serverFarms('sub-1', 'rg-test')->plan('my-plan')->get();

    expect($plan)->toBeInstanceOf(ServerFarmData::class)
        ->and($plan->skuName)->toBe('Y1')
        ->and($plan->skuTier)->toBe('Dynamic')
        ->and($plan->kind)->toBe('linux')
        ->and($plan->reserved)->toBeTrue()
        ->and($plan->status)->toBe('Ready')
        ->and($plan->location)->toBe('Switzerland North')
        ->and($plan->provisioningState)->toBe(ProvisioningState::Succeeded)
        ->and($plan->tags)->toBe(['project' => 'test']);
});

it('reads nothing rather than guessing when a plan omits sku and lifecycle', function (): void {
    $plan = ServerFarmData::fromAzure(['id' => '/plans/p', 'name' => 'p', 'location' => 'westeurope']);

    expect($plan->skuName)->toBeNull()
        ->and($plan->skuTier)->toBeNull()
        ->and($plan->kind)->toBeNull()
        ->and($plan->status)->toBeNull()
        ->and($plan->reserved)->toBeFalse()
        ->and($plan->provisioningState)->toBeNull()
        ->and($plan->tags)->toBe([]);
});

it('creates an app service plan through the resource gateway', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateServerFarm::class => MockResponse::make(body: serverFarmFixture()),
    ]);

    $plan = $client->serverFarms('sub-1', 'rg-test')->plan('my-plan')->createOrUpdate(
        location: 'switzerlandnorth',
        kind: 'linux',
        properties: ['reserved' => true],
        tags: ['project' => 'test'],
    );

    expect($plan->name)->toBe('my-plan');
});

it('observes an app service plan that is still provisioning', function (): void {
    $client = clientWithArmMock([
        GetServerFarm::class => MockResponse::make(body: serverFarmFixture('Creating')),
    ]);

    $observation = $client->serverFarms('sub-1', 'rg-test')->plan('my-plan')->observe();

    expect($observation->state)->toBe(ObservedState::Provisioning)
        ->and($observation->resource)->toBeInstanceOf(ServerFarmData::class);
});

it('observes a missing app service plan as absent rather than throwing', function (): void {
    $client = clientWithArmMock([
        GetServerFarm::class => MockResponse::make(
            body: ['error' => ['code' => 'ResourceNotFound', 'message' => 'no such plan']],
            status: 404,
        ),
    ]);

    $observation = $client->serverFarms('sub-1', 'rg-test')->plan('my-plan')->observe();

    expect($observation->state)->toBe(ObservedState::Absent)
        ->and($observation->resource)->toBeNull();
});

it('switches a publishing credentials policy off and reads the answer back', function (): void {
    $client = clientWithArmMock([
        UpdateBasicPublishingCredentialsPolicy::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Web/sites/my-func/basicPublishingCredentialsPolicies/scm',
            'name' => 'scm',
            'properties' => ['allow' => false],
        ]),
    ]);

    $policy = $client->functionApps('sub-1', 'rg-test')->app('my-func')
        ->updateBasicPublishingCredentialsPolicy('scm', false);

    expect($policy)->toBeInstanceOf(BasicPublishingCredentialsPolicyData::class)
        ->and($policy->name)->toBe('scm')
        ->and($policy->allow)->toBeFalse();
});

it('creates a storage table through the account gateway', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateStorageTable::class => MockResponse::make(body: [
            'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.Storage/storageAccounts/sa1/tableServices/default/tables/intaketargets',
            'name' => 'intaketargets',
            'properties' => ['tableName' => 'intaketargets'],
        ]),
    ]);

    $table = $client->storageAccounts('sub-1', 'rg-test')->account('sa1')->tables()->createOrUpdate('intaketargets');

    expect($table)->toBeInstanceOf(StorageTableData::class)
        ->and($table->name)->toBe('intaketargets')
        ->and($table->tableName)->toBe('intaketargets');
});

it('leaves the table name null when Azure omits it', function (): void {
    expect(StorageTableData::fromAzure(['id' => '/t', 'name' => 't'])->tableName)->toBeNull();
});
