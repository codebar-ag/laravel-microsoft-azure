<?php

use CodebarAg\MicrosoftAzure\Data\Arm\UserAssignedIdentityData;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\CreateOrUpdateUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\GetUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\ListUserAssignedIdentitiesByResourceGroup;
use CodebarAg\MicrosoftAzure\Resources\ManagedIdentitiesResource;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;

function userAssignedIdentityFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id1',
        'name' => 'id1',
        'location' => 'westeurope',
        'properties' => [
            'principalId' => '00000000-0000-0000-0000-000000000010',
            'clientId' => '00000000-0000-0000-0000-000000000020',
            'tenantId' => '00000000-0000-0000-0000-000000000030',
        ],
        'tags' => ['project' => 'test'],
    ];
}

it('lists user assigned identities in a resource group', function (): void {
    $client = clientWithArmMock([
        ListUserAssignedIdentitiesByResourceGroup::class => MockResponse::make(body: [
            'value' => [userAssignedIdentityFixture()],
        ]),
    ]);

    $identities = (new ManagedIdentitiesResource($client, 'sub-1', 'rg-test'))->list();

    expect($identities)->toBeInstanceOf(Collection::class)
        ->toHaveCount(1)
        ->and($identities->first()?->name)->toBe('id1');
});

it('gets a user assigned identity', function (): void {
    $client = clientWithArmMock([
        GetUserAssignedIdentity::class => MockResponse::make(body: userAssignedIdentityFixture()),
    ]);

    $identity = (new ManagedIdentitiesResource($client, 'sub-1', 'rg-test'))->identity('id1')->get();

    expect($identity)->toBeInstanceOf(UserAssignedIdentityData::class)
        ->and($identity->principalId)->toBe('00000000-0000-0000-0000-000000000010')
        ->and($identity->clientId)->toBe('00000000-0000-0000-0000-000000000020');
});

it('creates or updates a user assigned identity', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateUserAssignedIdentity::class => MockResponse::make(body: userAssignedIdentityFixture()),
    ]);

    $identity = (new ManagedIdentitiesResource($client, 'sub-1', 'rg-test'))->createOrUpdate('id1', 'westeurope');

    expect($identity->name)->toBe('id1')
        ->and($identity->tenantId)->toBe('00000000-0000-0000-0000-000000000030');
});
