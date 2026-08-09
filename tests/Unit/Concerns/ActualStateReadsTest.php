<?php

use CodebarAg\MicrosoftAzure\Data\Arm\ApplicationInsightsComponentData;
use CodebarAg\MicrosoftAzure\Data\Arm\CognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeletedVaultData;
use CodebarAg\MicrosoftAzure\Data\Arm\FoundryProjectData;
use CodebarAg\MicrosoftAzure\Data\Arm\KeyVaultData;
use CodebarAg\MicrosoftAzure\Data\Arm\ModelDeploymentData;
use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Data\Arm\StorageAccountData;
use CodebarAg\MicrosoftAzure\Data\Arm\WebSiteData;
use CodebarAg\MicrosoftAzure\Data\Graph\ApplicationData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Enums\ObservedState;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\BadRequestException;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts\GetCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\GetModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\GetFoundryProject;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults\ListDeletedVaults;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\GetComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\KeyVault\Vaults\GetVault;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Storage\GetStorageAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSite;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\GetApplication;
use CodebarAg\MicrosoftAzure\Requests\Graph\Applications\ListApplications;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\ListSecrets;
use CodebarAg\MicrosoftAzure\Resources\GraphResource;
use Saloon\Http\Faking\MockResponse;

/**
 * @param  array<string, mixed>  $properties
 * @return array<string, mixed>
 */
function observedArmFixture(string $name, array $properties = []): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Provider/things/'.$name,
        'name' => $name,
        'location' => 'westeurope',
        'properties' => $properties,
    ];
}

/**
 * @return array<string, mixed>
 */
function azureErrorFixture(string $message): array
{
    return ['error' => ['code' => 'Whatever', 'message' => $message]];
}

// ---------------------------------------------------------------------------
// ObservedState vocabulary
// ---------------------------------------------------------------------------

it('treats only an available resource as usable', function (ObservedState $state, bool $usable): void {
    expect($state->isUsable())->toBe($usable);
})->with([
    [ObservedState::Absent, false],
    [ObservedState::Provisioning, false],
    [ObservedState::Available, true],
    [ObservedState::Failed, false],
    [ObservedState::Deleting, false],
    [ObservedState::SoftDeleted, false],
    [ObservedState::Inaccessible, false],
]);

it('counts only live resources as present', function (ObservedState $state, bool $present): void {
    expect($state->isPresent())->toBe($present);
})->with([
    [ObservedState::Absent, false],
    [ObservedState::Provisioning, true],
    [ObservedState::Available, true],
    [ObservedState::Failed, true],
    [ObservedState::Deleting, true],
    // A corpse is not a resource, and 403 tells us nothing about existence.
    [ObservedState::SoftDeleted, false],
    [ObservedState::Inaccessible, false],
]);

it('knows which states hold a name against a create', function (ObservedState $state, bool $blocks): void {
    expect($state->blocksCreate())->toBe($blocks);
})->with([
    [ObservedState::Absent, false],
    [ObservedState::Provisioning, false],
    [ObservedState::Available, false],
    [ObservedState::Failed, false],
    [ObservedState::Deleting, true],
    [ObservedState::SoftDeleted, true],
    [ObservedState::Inaccessible, false],
]);

it('knows which states are worth re-reading', function (ObservedState $state, bool $settled): void {
    expect($state->isSettled())->toBe($settled);
})->with([
    [ObservedState::Absent, true],
    [ObservedState::Provisioning, false],
    [ObservedState::Available, true],
    [ObservedState::Failed, true],
    [ObservedState::Deleting, false],
    [ObservedState::SoftDeleted, true],
    // Resolves itself once an RBAC grant propagates — keep polling.
    [ObservedState::Inaccessible, false],
]);

// ---------------------------------------------------------------------------
// Mapping a body to a state
// ---------------------------------------------------------------------------

it('maps a reported provisioning state to an observed state', function (string $reported, ObservedState $expected): void {
    $observation = ResourceObservationData::fromAzure(
        observedArmFixture('thing', ['provisioningState' => $reported]),
    );

    expect($observation->state)->toBe($expected)
        ->and($observation->reportedProvisioningState)->toBe($reported);
})->with([
    ['Succeeded', ObservedState::Available],
    ['Failed', ObservedState::Failed],
    ['Canceled', ObservedState::Failed],
    ['Deleting', ObservedState::Deleting],
    ['Creating', ObservedState::Provisioning],
    ['Accepted', ObservedState::Provisioning],
    ['Updating', ObservedState::Provisioning],
    ['Moving', ObservedState::Provisioning],
    ['ResolvingDNS', ObservedState::Provisioning],
    ['RegisteringDns', ObservedState::Provisioning],
    ['NotSpecified', ObservedState::Provisioning],
]);

it('never reports an unrecognised provisioning state as usable', function (): void {
    $observation = ResourceObservationData::fromAzure(
        observedArmFixture('thing', ['provisioningState' => 'SomeStateAzureAddedLater']),
    );

    expect($observation->state)->toBe(ObservedState::Provisioning)
        ->and($observation->provisioningState)->toBeNull()
        ->and($observation->reportedProvisioningState)->toBe('SomeStateAzureAddedLater');
});

it('treats a body without any provisioning state as available', function (): void {
    $observation = ResourceObservationData::fromAzure(['id' => 'app-1']);

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($observation->reportedProvisioningState)->toBeNull()
        ->and($observation->provisioningState)->toBeNull();
});

it('recognises the provisioning states providers actually emit', function (): void {
    expect(ProvisioningState::tryFrom('ResolvingDNS'))->toBe(ProvisioningState::ResolvingDNS)
        ->and(ProvisioningState::tryFrom('RegisteringDns'))->toBe(ProvisioningState::RegisteringDns)
        ->and(ProvisioningState::tryFrom('Moving'))->toBe(ProvisioningState::Moving)
        ->and(ProvisioningState::ResolvingDNS->isTerminal())->toBeFalse()
        ->and(ProvisioningState::RegisteringDns->isTerminal())->toBeFalse()
        ->and(ProvisioningState::Moving->isTerminal())->toBeFalse();
});

it('builds absent, inaccessible and soft-deleted observations directly', function (): void {
    $absent = ResourceObservationData::absent(404, 'gone');
    $inaccessible = ResourceObservationData::inaccessible(403, 'nope');
    $softDeleted = ResourceObservationData::softDeleted();

    expect($absent->state)->toBe(ObservedState::Absent)
        ->and($absent->statusCode)->toBe(404)
        ->and($absent->message)->toBe('gone')
        ->and($inaccessible->state)->toBe(ObservedState::Inaccessible)
        ->and($inaccessible->statusCode)->toBe(403)
        ->and($inaccessible->message)->toBe('nope')
        ->and($softDeleted->state)->toBe(ObservedState::SoftDeleted)
        ->and($softDeleted->resource)->toBeNull();
});

// ---------------------------------------------------------------------------
// Absent / inaccessible / unexpected, via a resource gateway
// ---------------------------------------------------------------------------

it('observes a resource group that exists', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(body: resourceGroupFixture()),
    ]);

    $observation = $client->resourceGroups('sub-1')->observe('rg-test');

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($observation->state->isUsable())->toBeTrue()
        ->and($observation->name)->toBe('rg-test')
        ->and($observation->location)->toBe('westeurope')
        ->and($observation->statusCode)->toBe(200)
        ->and($observation->provisioningState)->toBe(ProvisioningState::Succeeded)
        ->and($observation->resource)->toBeInstanceOf(ResourceGroupData::class);
});

it('reports a resource deleted out of band as absent instead of throwing', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(
            body: azureErrorFixture('Resource group not found.'),
            status: 404,
        ),
    ]);

    $observation = $client->resourceGroups('sub-1')->observe('rg-test');

    expect($observation->state)->toBe(ObservedState::Absent)
        ->and($observation->statusCode)->toBe(404)
        ->and($observation->message)->toBe('Resource group not found.')
        ->and($observation->resource)->toBeNull();
});

it('reports a forbidden read as inaccessible rather than absent', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(
            body: azureErrorFixture('The client does not have authorization.'),
            status: 403,
        ),
    ]);

    $observation = $client->resourceGroups('sub-1')->observe('rg-test');

    expect($observation->state)->toBe(ObservedState::Inaccessible)
        ->and($observation->state->isPresent())->toBeFalse()
        ->and($observation->state->isSettled())->toBeFalse()
        ->and($observation->statusCode)->toBe(403);
});

it('still throws for failures that say nothing about the resource', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(body: azureErrorFixture('Bad request.'), status: 400),
    ]);

    $client->resourceGroups('sub-1')->observe('rg-test');
})->throws(BadRequestException::class);

// ---------------------------------------------------------------------------
// Soft delete
// ---------------------------------------------------------------------------

it('reports a soft-deleted vault as soft-deleted, not absent', function (): void {
    $client = clientWithArmMock([
        GetVault::class => MockResponse::make(body: azureErrorFixture('Vault not found.'), status: 404),
        ListDeletedVaults::class => MockResponse::make(body: ['value' => [[
            'id' => '/subscriptions/sub-1/providers/Microsoft.KeyVault/locations/westeurope/deletedVaults/kv-test',
            'name' => 'KV-Test',
            'location' => 'westeurope',
            'properties' => [
                'deletionDate' => '2026-01-01T00:00:00Z',
                'scheduledPurgeDate' => '2026-04-01T00:00:00Z',
            ],
        ]]]),
    ]);

    $observation = $client->vaults('sub-1', 'rg-test')->vault('kv-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::SoftDeleted)
        ->and($observation->state->blocksCreate())->toBeTrue()
        ->and($observation->state->isPresent())->toBeFalse()
        ->and($observation->scheduledPurgeDate)->toBe('2026-04-01T00:00:00Z')
        ->and($observation->resource)->toBeInstanceOf(DeletedVaultData::class);
});

it('reports an absent vault as absent when nothing holds its name', function (): void {
    $client = clientWithArmMock([
        GetVault::class => MockResponse::make(body: azureErrorFixture('Vault not found.'), status: 404),
        ListDeletedVaults::class => MockResponse::make(body: ['value' => []]),
    ]);

    $observation = $client->vaults('sub-1', 'rg-test')->vault('kv-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::Absent);
});

it('skips the soft-delete probe when no location is given', function (): void {
    $client = clientWithArmMock([
        GetVault::class => MockResponse::make(body: azureErrorFixture('Vault not found.'), status: 404),
    ]);

    $observation = $client->vaults('sub-1', 'rg-test')->vault('kv-test')->observe();

    expect($observation->state)->toBe(ObservedState::Absent);
});

it('does not claim a vault is absent when the soft-delete probe itself is refused', function (): void {
    $client = clientWithArmMock([
        GetVault::class => MockResponse::make(body: azureErrorFixture('Vault not found.'), status: 404),
        ListDeletedVaults::class => MockResponse::make(body: azureErrorFixture('Forbidden.'), status: 403),
    ]);

    $observation = $client->vaults('sub-1', 'rg-test')->vault('kv-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::Inaccessible)
        ->and($observation->statusCode)->toBe(403);
});

it('observes a live vault', function (): void {
    $client = clientWithArmMock([
        GetVault::class => MockResponse::make(body: observedArmFixture('kv-test', [
            'provisioningState' => 'RegisteringDns',
            'vaultUri' => 'https://kv-test.vault.azure.net/',
        ])),
    ]);

    $observation = $client->vaults('sub-1', 'rg-test')->vault('kv-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::Provisioning)
        ->and($observation->state->isUsable())->toBeFalse()
        ->and($observation->resource)->toBeInstanceOf(KeyVaultData::class);
});

it('reports a soft-deleted foundry account, which still holds its model quota', function (): void {
    $client = clientWithArmMock([
        GetCognitiveServicesAccount::class => MockResponse::make(
            body: azureErrorFixture('Account not found.'),
            status: 404,
        ),
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => [[
            'id' => '/subscriptions/sub-1/providers/Microsoft.CognitiveServices/locations/westeurope/resourceGroups/rg-test/deletedAccounts/aif-test',
            'name' => 'aif-test',
            'location' => 'westeurope',
            'properties' => ['scheduledPurgeDate' => '2026-04-01T00:00:00Z'],
        ]]]),
    ]);

    $observation = $client->cognitiveServices('sub-1', 'rg-test')->account('aif-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::SoftDeleted)
        ->and($observation->state->blocksCreate())->toBeTrue()
        ->and($observation->scheduledPurgeDate)->toBe('2026-04-01T00:00:00Z')
        ->and($observation->resource)->toBeInstanceOf(DeletedCognitiveServicesAccountData::class);
});

it('reports an absent foundry account as absent when nothing holds its name', function (): void {
    $client = clientWithArmMock([
        GetCognitiveServicesAccount::class => MockResponse::make(
            body: azureErrorFixture('Account not found.'),
            status: 404,
        ),
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => []]),
    ]);

    $observation = $client->cognitiveServices('sub-1', 'rg-test')->account('aif-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::Absent);
});

it('does not claim a foundry account is absent when the soft-delete probe is refused', function (): void {
    $client = clientWithArmMock([
        GetCognitiveServicesAccount::class => MockResponse::make(
            body: azureErrorFixture('Account not found.'),
            status: 404,
        ),
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(
            body: azureErrorFixture('Forbidden.'),
            status: 403,
        ),
    ]);

    $observation = $client->cognitiveServices('sub-1', 'rg-test')->account('aif-test')->observe('westeurope');

    expect($observation->state)->toBe(ObservedState::Inaccessible);
});

it('observes a live foundry account', function (): void {
    $client = clientWithArmMock([
        GetCognitiveServicesAccount::class => MockResponse::make(
            body: observedArmFixture('aif-test', ['provisioningState' => 'Succeeded']),
        ),
    ]);

    $observation = $client->cognitiveServices('sub-1', 'rg-test')->account('aif-test')->observe();

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($observation->resource)->toBeInstanceOf(CognitiveServicesAccountData::class);
});

it('finds a soft-deleted resource by name regardless of case', function (): void {
    $client = clientWithArmMock([
        ListDeletedVaults::class => MockResponse::make(body: ['value' => [deletedVaultFixture()]]),
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => [[
            'id' => '/subscriptions/sub-1/deletedAccounts/aif-test',
            'name' => 'aif-test',
            'location' => 'westeurope',
        ]]]),
    ]);

    expect($client->deletedVaults('sub-1')->find('westeurope', 'KV-TEST'))->toBeInstanceOf(DeletedVaultData::class)
        ->and($client->deletedVaults('sub-1')->find('westeurope', 'other-vault'))->toBeNull()
        ->and($client->deletedCognitiveServices('sub-1')->find('westeurope', 'AIF-Test'))
        ->toBeInstanceOf(DeletedCognitiveServicesAccountData::class)
        ->and($client->deletedCognitiveServices('sub-1')->find('westeurope', 'other'))->toBeNull();
});

// ---------------------------------------------------------------------------
// The remaining resource families
// ---------------------------------------------------------------------------

it('observes a foundry project', function (): void {
    $client = clientWithArmMock([
        GetFoundryProject::class => MockResponse::make(
            body: observedArmFixture('prj-test', ['provisioningState' => 'Creating']),
        ),
    ]);

    $observation = $client->cognitiveServices('sub-1', 'rg-test')->account('aif-test')->projects()->observe('prj-test');

    expect($observation->state)->toBe(ObservedState::Provisioning)
        ->and($observation->resource)->toBeInstanceOf(FoundryProjectData::class);
});

it('observes a model deployment', function (): void {
    $client = clientWithArmMock([
        GetModelDeployment::class => MockResponse::make(
            body: observedArmFixture('gpt-4o', ['provisioningState' => 'Failed']),
        ),
    ]);

    $observation = $client->cognitiveServices('sub-1', 'rg-test')->account('aif-test')->deployments()->observe('gpt-4o');

    expect($observation->state)->toBe(ObservedState::Failed)
        ->and($observation->state->isSettled())->toBeTrue()
        ->and($observation->state->isUsable())->toBeFalse()
        ->and($observation->resource)->toBeInstanceOf(ModelDeploymentData::class);
});

it('observes a storage account still resolving DNS as not yet usable', function (): void {
    $client = clientWithArmMock([
        GetStorageAccount::class => MockResponse::make(
            body: observedArmFixture('sa1', ['provisioningState' => 'ResolvingDNS']),
        ),
    ]);

    $observation = $client->storageAccounts('sub-1', 'rg-test')->account('sa1')->observe();

    expect($observation->state)->toBe(ObservedState::Provisioning)
        ->and($observation->provisioningState)->toBe(ProvisioningState::ResolvingDNS)
        ->and($observation->resource)->toBeInstanceOf(StorageAccountData::class);
});

it('observes an application insights component', function (): void {
    $client = clientWithArmMock([
        GetComponent::class => MockResponse::make(
            body: observedArmFixture('ai-test', ['provisioningState' => 'Succeeded', 'AppId' => 'app-1']),
        ),
    ]);

    $observation = $client->applicationInsights('sub-1', 'rg-test')->component('ai-test')->observe();

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($observation->resource)->toBeInstanceOf(ApplicationInsightsComponentData::class);
});

it('observes a function app that is provisioned but stopped as available', function (): void {
    $client = clientWithArmMock([
        GetSite::class => MockResponse::make(
            body: observedArmFixture('my-func', ['provisioningState' => 'Succeeded', 'state' => 'Stopped']),
        ),
    ]);

    $observation = $client->functionApps('sub-1', 'rg-test')->app('my-func')->observe();

    $site = $observation->resource;

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($site)->toBeInstanceOf(WebSiteData::class)
        ->and($site->state)->toBe('Stopped');
});

it('observes a resource being deleted as deleting, not absent', function (): void {
    $client = clientWithArmMock([
        GetSite::class => MockResponse::make(
            body: observedArmFixture('my-func', ['provisioningState' => 'Deleting']),
        ),
    ]);

    $observation = $client->functionApps('sub-1', 'rg-test')->app('my-func')->observe();

    expect($observation->state)->toBe(ObservedState::Deleting)
        ->and($observation->state->blocksCreate())->toBeTrue()
        ->and($observation->state->isSettled())->toBeFalse();
});

// ---------------------------------------------------------------------------
// Entra app registrations (Microsoft Graph)
// ---------------------------------------------------------------------------

it('reads an app registration by object id', function (): void {
    $client = clientWithGraphMock([
        GetApplication::class => MockResponse::make(body: applicationFixture()),
    ]);

    expect((new GraphResource($client))->applications()->get('app-object-1')->appId)
        ->toBe('00000000-0000-0000-0000-000000000010');
});

it('lists app registrations and finds one by client id', function (): void {
    $client = clientWithGraphMock([
        ListApplications::class => MockResponse::make(body: ['value' => [applicationFixture()]]),
    ]);

    $applications = (new GraphResource($client))->applications();

    expect($applications->list())->toHaveCount(1)
        ->and($applications->findByAppId('00000000-0000-0000-0000-000000000010'))
        ->toBeInstanceOf(ApplicationData::class);
});

it('returns null when no app registration carries the client id', function (): void {
    $client = clientWithGraphMock([
        ListApplications::class => MockResponse::make(body: ['value' => []]),
    ]);

    expect((new GraphResource($client))->applications()->findByAppId('missing'))->toBeNull();
});

it('observes an app registration', function (): void {
    $client = clientWithGraphMock([
        GetApplication::class => MockResponse::make(body: applicationFixture()),
    ]);

    $observation = (new GraphResource($client))->applications()->observe('app-object-1');

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($observation->resource)->toBeInstanceOf(ApplicationData::class);
});

it('observes an app registration deleted out of band as absent', function (): void {
    $client = clientWithGraphMock([
        GetApplication::class => MockResponse::make(
            body: azureErrorFixture('Resource does not exist.'),
            status: 404,
        ),
    ]);

    expect((new GraphResource($client))->applications()->observe('app-object-1')->state)->toBe(ObservedState::Absent);
});

// ---------------------------------------------------------------------------
// Data-plane reachability — the question ARM cannot answer
// ---------------------------------------------------------------------------

it('observes key vault data-plane access as available', function (): void {
    $client = clientWithKeyVaultMock([
        ListSecrets::class => MockResponse::make(body: ['value' => []]),
    ], 'my-kv');

    $observation = $client->vault('my-kv')->secrets()->observeAccess();

    expect($observation->state)->toBe(ObservedState::Available)
        ->and($observation->resource)->toBeNull();
});

it('observes key vault data-plane access as inaccessible while a grant propagates', function (): void {
    $client = clientWithKeyVaultMock([
        ListSecrets::class => MockResponse::make(
            body: azureErrorFixture('Caller is not authorized to perform action on resource.'),
            status: 403,
        ),
    ], 'my-kv');

    $observation = $client->vault('my-kv')->secrets()->observeAccess();

    expect($observation->state)->toBe(ObservedState::Inaccessible)
        ->and($observation->state->isSettled())->toBeFalse();
});
