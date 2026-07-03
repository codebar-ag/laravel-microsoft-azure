<?php

use CodebarAg\MicrosoftAzure\Data\Arm\ApiManagementServiceData;
use CodebarAg\MicrosoftAzure\Data\Arm\ApimSubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Arm\ApimSubscriptionKeysData;
use CodebarAg\MicrosoftAzure\Enums\ApimSubscriptionState;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service\CreateOrUpdateApiManagementService;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service\DeleteApiManagementService;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Service\GetApiManagementService;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\CreateOrUpdateApimSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\GetApimSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\ListApimSubscriptions;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\ListApimSubscriptionSecrets;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\RegenerateApimSubscriptionPrimaryKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\RegenerateApimSubscriptionSecondaryKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement\Subscriptions\UpdateApimSubscriptionState;
use CodebarAg\MicrosoftAzure\Resources\ApiManagementResource;
use Saloon\Http\Faking\MockResponse;

function apimSubscriptionFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ApiManagement/service/my-apim/subscriptions/partner-a',
        'name' => 'partner-a',
        'properties' => [
            'displayName' => 'Partner A',
            'scope' => '/apis',
            'state' => 'active',
            'ownerId' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ApiManagement/service/my-apim/users/1',
            'createdDate' => '2026-01-01T00:00:00Z',
            'allowTracing' => true,
        ],
    ];
}

function apiManagementServiceFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ApiManagement/service/my-apim',
        'name' => 'my-apim',
        'location' => 'westeurope',
        'sku' => ['name' => 'Consumption', 'capacity' => 0],
        'properties' => [
            'provisioningState' => 'Succeeded',
            'gatewayUrl' => 'https://my-apim.azure-api.net',
        ],
    ];
}

it('creates, gets and deletes an api management service', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateApiManagementService::class => MockResponse::make(body: apiManagementServiceFixture()),
        GetApiManagementService::class => MockResponse::make(body: apiManagementServiceFixture()),
        DeleteApiManagementService::class => MockResponse::make(body: '', status: 202),
    ]);

    $gateway = new ApiManagementResource($client, 'sub-1', 'rg-test');

    $created = $gateway->createOrUpdate('my-apim', 'westeurope', 'test@example.com', 'Test Publisher');
    $fetched = $gateway->service('my-apim')->get();
    $gateway->service('my-apim')->delete();

    expect($created)->toBeInstanceOf(ApiManagementServiceData::class)
        ->and($created->provisioningState)->toBe('Succeeded')
        ->and($created->gatewayUrl)->toBe('https://my-apim.azure-api.net')
        ->and($fetched->name)->toBe('my-apim');
});

it('creates, gets and lists apim subscriptions', function (): void {
    $client = clientWithArmMock([
        CreateOrUpdateApimSubscription::class => MockResponse::make(body: apimSubscriptionFixture()),
        GetApimSubscription::class => MockResponse::make(body: apimSubscriptionFixture()),
        ListApimSubscriptions::class => MockResponse::make(body: ['value' => [apimSubscriptionFixture()]]),
    ]);

    $subscriptions = (new ApiManagementResource($client, 'sub-1', 'rg-test'))
        ->service('my-apim')
        ->subscriptions();

    $created = $subscriptions->create('partner-a', 'Partner A');
    $fetched = $subscriptions->get('partner-a');
    $listed = $subscriptions->list();

    expect($created)->toBeInstanceOf(ApimSubscriptionData::class)
        ->and($created->displayName)->toBe('Partner A')
        ->and($created->state)->toBe(ApimSubscriptionState::Active)
        ->and($created->allowTracing)->toBeTrue()
        ->and($fetched->name)->toBe('partner-a')
        ->and($listed)->toHaveCount(1)
        ->and($listed->first())->toBeInstanceOf(ApimSubscriptionData::class);
});

it('regenerates keys, revokes and lists secrets for an apim subscription', function (): void {
    $client = clientWithArmMock([
        RegenerateApimSubscriptionPrimaryKey::class => MockResponse::make(body: '', status: 204),
        RegenerateApimSubscriptionSecondaryKey::class => MockResponse::make(body: '', status: 204),
        UpdateApimSubscriptionState::class => MockResponse::make(body: apimSubscriptionFixture()),
        ListApimSubscriptionSecrets::class => MockResponse::make(body: [
            'primaryKey' => 'primary-secret',
            'secondaryKey' => 'secondary-secret',
        ]),
    ]);

    $subscription = (new ApiManagementResource($client, 'sub-1', 'rg-test'))
        ->service('my-apim')
        ->subscriptions()
        ->subscription('partner-a');

    $subscription->regeneratePrimaryKey();
    $subscription->regenerateSecondaryKey();
    $subscription->revoke();
    $keys = $subscription->listSecrets();

    expect($keys)->toBeInstanceOf(ApimSubscriptionKeysData::class)
        ->and($keys->primaryKey)->toBe('primary-secret')
        ->and($keys->secondaryKey)->toBe('secondary-secret');
});
