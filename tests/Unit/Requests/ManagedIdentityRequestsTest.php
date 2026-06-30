<?php

use CodebarAg\MicrosoftAzure\Data\Payload\UserAssignedIdentityPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\CreateOrUpdateUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\DeleteUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\GetUserAssignedIdentity;
use CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity\ListUserAssignedIdentitiesByResourceGroup;
use Saloon\Http\Request;

dataset('managed identity request endpoints', [
    'GetUserAssignedIdentity' => [
        fn () => new GetUserAssignedIdentity('sub-1', 'rg-test', 'id1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id1',
        ApiVersion::ARM_MANAGED_IDENTITY,
    ],
    'ListUserAssignedIdentitiesByResourceGroup' => [
        fn () => new ListUserAssignedIdentitiesByResourceGroup('sub-1', 'rg-test'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ManagedIdentity/userAssignedIdentities',
        ApiVersion::ARM_MANAGED_IDENTITY,
    ],
    'CreateOrUpdateUserAssignedIdentity' => [
        fn () => new CreateOrUpdateUserAssignedIdentity('sub-1', 'rg-test', 'id1', new UserAssignedIdentityPayload('westeurope')),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id1',
        ApiVersion::ARM_MANAGED_IDENTITY,
    ],
    'DeleteUserAssignedIdentity' => [
        fn () => new DeleteUserAssignedIdentity('sub-1', 'rg-test', 'id1'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id1',
        ApiVersion::ARM_MANAGED_IDENTITY,
    ],
]);

it('resolves managed identity request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('managed identity request endpoints');

it('builds create user assigned identity body with location only', function (): void {
    $request = new CreateOrUpdateUserAssignedIdentity('sub-1', 'rg-test', 'id1', new UserAssignedIdentityPayload('westeurope'));

    expect($request->body()->all())->toBe(['location' => 'westeurope']);
});

it('builds create user assigned identity body with tags', function (): void {
    $request = new CreateOrUpdateUserAssignedIdentity(
        'sub-1',
        'rg-test',
        'id1',
        new UserAssignedIdentityPayload('westeurope', ['project' => 'test']),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'tags' => ['project' => 'test'],
    ]);
});
