<?php

use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData;
use CodebarAg\MicrosoftAzure\Data\Graph\GroupData;
use CodebarAg\MicrosoftAzure\Data\KeyVault\SecretData;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionState;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;
use CodebarAg\MicrosoftAzure\Enums\TokenAudience;
use CodebarAg\MicrosoftAzure\Exceptions\DeploymentFailedException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\ListDeploymentOperations;
use CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups\GetResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\GetGroup;
use CodebarAg\MicrosoftAzure\Requests\Graph\Groups\ListGroups;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\GetSecret;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\ListSecrets;
use CodebarAg\MicrosoftAzure\Requests\KeyVault\SetSecret;
use CodebarAg\MicrosoftAzure\Resources\GraphResource;
use Saloon\Http\Faking\MockResponse;

it('lists key vault secrets via vault resource gateway', function (): void {
    $client = clientWithKeyVaultMock([
        ListSecrets::class => MockResponse::make(body: [
            'value' => [secretIdentifierFixture()],
        ]),
    ]);

    $secrets = $client->vault('myvault')->secrets()->list();

    expect($secrets)->toHaveCount(1)
        ->and($secrets->first()?->enabled)->toBeTrue();
});

it('sets key vault secrets via secrets resource gateway', function (): void {
    $client = clientWithKeyVaultMock([
        SetSecret::class => MockResponse::make(body: secretFixture()),
    ]);

    $secret = $client->secrets('myvault')->set('webhook-token', 'value');

    expect($secret->name)->toBe('webhook-token');
});

it('lists graph groups via graph resource gateway', function (): void {
    $client = clientWithGraphMock([
        ListGroups::class => MockResponse::make(body: [
            'value' => [groupFixture()],
        ]),
    ]);

    $groups = (new GraphResource($client))->groups()->list();

    expect($groups)->toHaveCount(1)
        ->and($groups->first()?->displayName)->toBe('Readers');
});

it('lists deployment operations via deployments resource gateway', function (): void {
    $client = clientWithArmMock([
        ListDeploymentOperations::class => MockResponse::make(body: [
            'value' => [deploymentOperationFixture()],
        ]),
    ]);

    $operations = $client->deployments('sub-1', 'rg-test')->operations('tenantflow');

    expect($operations)->toHaveCount(1)
        ->and($operations->first()?->operationId)->toBe('op-1');
});

it('throws deployment failed exception when create returns failed state', function (): void {
    $payload = deploymentFixture();
    $payload['properties']['provisioningState'] = 'Failed';

    $client = clientWithArmMock([
        CreateOrUpdateDeployment::class => MockResponse::make(body: $payload),
    ]);

    expect(fn () => $client->deployments('sub-1', 'rg-test')->createOrUpdate(
        'tenantflow',
        ['$schema' => 'https://schema.management.azure.com/schemas/2019-04-01/deploymentTemplate.json#'],
    ))->toThrow(DeploymentFailedException::class);
});

it('reads successful deployment via deployments resource gateway', function (): void {
    $client = clientWithArmMock([
        GetDeployment::class => MockResponse::make(body: deploymentFixture()),
    ]);

    $deployment = $client->deployments('sub-1', 'rg-test')->get('tenantflow');

    expect($deployment)->toBeInstanceOf(DeploymentData::class)
        ->and($deployment->name)->toBe('tenantflow');
});

it('maps api version constants', function (): void {
    expect(ApiVersion::ARM_RESOURCES)->toBe('2025-04-01')
        ->and(ApiVersion::KEY_VAULT)->toBe('2025-07-01')
        ->and(ApiVersion::GRAPH)->toBe('v1.0');
});

it('maps subscription enums', function (): void {
    expect(SubscriptionState::Enabled->value)->toBe('Enabled')
        ->and(SubscriptionWorkload::DevTest->value)->toBe('DevTest');
});

it('maps token audience scopes', function (): void {
    expect(TokenAudience::Arm->scope())->toBe('https://management.azure.com/.default')
        ->and(TokenAudience::Graph->scope())->toBe('https://graph.microsoft.com/.default');
});

it('throws not found when resource group get fails', function (): void {
    $client = clientWithArmMock([
        GetResourceGroup::class => MockResponse::make(body: ['message' => 'missing'], status: 404),
    ]);

    expect(fn () => $client->resourceGroups('sub-1')->get('missing'))
        ->toThrow(NotFoundException::class);
});

it('deserializes secret data through secrets resource get', function (): void {
    $client = clientWithKeyVaultMock([
        GetSecret::class => MockResponse::make(body: secretFixture()),
    ]);

    $secret = $client->vault('myvault')->secrets()->get('webhook-token');

    expect($secret)->toBeInstanceOf(SecretData::class)
        ->and($secret->value)->toBe('secret-value');
});

it('deserializes group data through graph groups get', function (): void {
    $client = clientWithGraphMock([
        GetGroup::class => MockResponse::make(body: groupFixture()),
    ]);

    $group = (new GraphResource($client))->groups()->get('group-1');

    expect($group)->toBeInstanceOf(GroupData::class)
        ->and($group->mailNickname)->toBe('readers');
});
