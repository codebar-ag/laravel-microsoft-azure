<?php

use CodebarAg\MicrosoftAzure\Data\Arm\ResourceProviderData;
use CodebarAg\MicrosoftAzure\Data\Arm\RoleDefinitionData;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\GetResourceProvider;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\ListResourceProviders;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders\RegisterResourceProvider;
use CodebarAg\MicrosoftAzure\Requests\Arm\RoleDefinitions\ListRoleDefinitions;
use Saloon\Http\Faking\MockResponse;

function resourceProviderFixture(string $state = 'Registered'): array
{
    return [
        'id' => '/subscriptions/sub-1/providers/Microsoft.Logic',
        'namespace' => 'Microsoft.Logic',
        'registrationState' => $state,
    ];
}

it('lists, gets and registers resource providers', function (): void {
    $client = clientWithArmMock([
        ListResourceProviders::class => MockResponse::make(body: ['value' => [resourceProviderFixture()]]),
        GetResourceProvider::class => MockResponse::make(body: resourceProviderFixture()),
        RegisterResourceProvider::class => MockResponse::make(body: resourceProviderFixture('Registering')),
    ]);

    $providers = $client->resourceProviders('sub-1');

    $listed = $providers->list();
    $fetched = $providers->get('Microsoft.Logic');
    $registered = $providers->register('Microsoft.Logic');

    expect($listed)->toHaveCount(1)
        ->and($listed->first())->toBeInstanceOf(ResourceProviderData::class)
        ->and($fetched->isRegistered())->toBeTrue()
        ->and($fetched->isRegistering())->toBeFalse()
        ->and($registered->isRegistering())->toBeTrue();
});

it('awaits provider registration until registered', function (): void {
    $client = clientWithArmMock([
        MockResponse::make(body: resourceProviderFixture('Registering')),
        MockResponse::make(body: resourceProviderFixture('Registered')),
    ]);

    $provider = $client->resourceProviders('sub-1')->awaitRegistered('Microsoft.Logic', 30, 0);

    expect($provider->isRegistered())->toBeTrue();
});

it('throws when provider registration finishes in an unexpected state', function (): void {
    $client = clientWithArmMock([
        GetResourceProvider::class => MockResponse::make(body: resourceProviderFixture('NotRegistered')),
    ]);

    $client->resourceProviders('sub-1')->awaitRegistered('Microsoft.Logic');
})->throws(LongRunningOperationException::class, 'finished in state');

it('throws when provider registration does not finish in time', function (): void {
    $client = clientWithArmMock([
        GetResourceProvider::class => MockResponse::make(body: resourceProviderFixture('Registering')),
    ]);

    $client->resourceProviders('sub-1')->awaitRegistered('Microsoft.Logic', 0, 1);
})->throws(LongRunningOperationException::class, 'did not register within');

it('lists role definitions and finds them by name', function (): void {
    $definitionFixture = [
        'id' => '/subscriptions/sub-1/providers/Microsoft.Authorization/roleDefinitions/def-1',
        'name' => 'def-1',
        'type' => 'Microsoft.Authorization/roleDefinitions',
        'properties' => ['roleName' => 'Contributor'],
    ];

    $client = clientWithArmMock([
        ListRoleDefinitions::class => MockResponse::make(body: ['value' => [$definitionFixture]]),
    ]);

    $definitions = $client->roleDefinitions('sub-1');

    expect($definitions->list())->toHaveCount(1)
        ->and($definitions->list()->first())->toBeInstanceOf(RoleDefinitionData::class)
        ->and($definitions->findByName('Contributor')->roleName)->toBe('Contributor');
});

it('throws when a role definition is missing', function (): void {
    $client = clientWithArmMock([
        ListRoleDefinitions::class => MockResponse::make(body: ['value' => []]),
    ]);

    $client->roleDefinitions('sub-1')->findByName('Owner');
})->throws(RuntimeException::class, 'was not found');

it('includes the filter query on role definition list requests', function (): void {
    $unfiltered = new ListRoleDefinitions('sub-1');
    $filtered = new ListRoleDefinitions('sub-1', "roleName eq 'Contributor'");

    expect($unfiltered->query()->all())->not->toHaveKey('$filter')
        ->and($filtered->query()->all()['$filter'])->toBe("roleName eq 'Contributor'");
});
