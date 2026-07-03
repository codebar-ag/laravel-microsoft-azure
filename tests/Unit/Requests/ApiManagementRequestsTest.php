<?php

use CodebarAg\MicrosoftAzure\Data\Payload\ApiManagementServicePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\ApimSubscriptionPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
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
use Saloon\Http\Request;

$apimBase = '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ApiManagement/service/my-apim/subscriptions/partner-a';
$apimServiceBase = '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ApiManagement/service/my-apim';

dataset('api management request endpoints', [
    'CreateOrUpdateApiManagementService' => [
        fn () => new CreateOrUpdateApiManagementService('sub-1', 'rg-test', 'my-apim', new ApiManagementServicePayload('westeurope', 'test@example.com', 'Test')),
        $apimServiceBase,
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'GetApiManagementService' => [
        fn () => new GetApiManagementService('sub-1', 'rg-test', 'my-apim'),
        $apimServiceBase,
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'DeleteApiManagementService' => [
        fn () => new DeleteApiManagementService('sub-1', 'rg-test', 'my-apim'),
        $apimServiceBase,
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'CreateOrUpdateApimSubscription' => [
        fn () => new CreateOrUpdateApimSubscription('sub-1', 'rg-test', 'my-apim', 'partner-a', new ApimSubscriptionPayload('/apis', 'Partner A')),
        $apimBase,
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'GetApimSubscription' => [
        fn () => new GetApimSubscription('sub-1', 'rg-test', 'my-apim', 'partner-a'),
        $apimBase,
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'ListApimSubscriptions' => [
        fn () => new ListApimSubscriptions('sub-1', 'rg-test', 'my-apim'),
        '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.ApiManagement/service/my-apim/subscriptions',
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'RegenerateApimSubscriptionPrimaryKey' => [
        fn () => new RegenerateApimSubscriptionPrimaryKey('sub-1', 'rg-test', 'my-apim', 'partner-a'),
        $apimBase.'/regeneratePrimaryKey',
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'RegenerateApimSubscriptionSecondaryKey' => [
        fn () => new RegenerateApimSubscriptionSecondaryKey('sub-1', 'rg-test', 'my-apim', 'partner-a'),
        $apimBase.'/regenerateSecondaryKey',
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'UpdateApimSubscriptionState' => [
        fn () => new UpdateApimSubscriptionState('sub-1', 'rg-test', 'my-apim', 'partner-a', new GenericJsonPayload(['properties' => ['state' => 'suspended']])),
        $apimBase,
        ApiVersion::ARM_API_MANAGEMENT,
    ],
    'ListApimSubscriptionSecrets' => [
        fn () => new ListApimSubscriptionSecrets('sub-1', 'rg-test', 'my-apim', 'partner-a'),
        $apimBase.'/listSecrets',
        ApiVersion::ARM_API_MANAGEMENT,
    ],
]);

it('resolves api management request endpoints and api-version query', function (callable $factory, string $endpoint, ApiVersion $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion->value()]);
})->with('api management request endpoints');

it('builds create or update api management service body', function (): void {
    $request = new CreateOrUpdateApiManagementService(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serviceName: 'my-apim',
        payload: new ApiManagementServicePayload('westeurope', 'test@example.com', 'Test Publisher'),
    );

    expect($request->body()->all())->toBe([
        'location' => 'westeurope',
        'sku' => ['name' => 'Consumption', 'capacity' => 0],
        'properties' => [
            'publisherEmail' => 'test@example.com',
            'publisherName' => 'Test Publisher',
        ],
    ]);
});

it('builds create or update apim subscription body with default scope', function (): void {
    $request = new CreateOrUpdateApimSubscription(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serviceName: 'my-apim',
        apimSubscriptionId: 'partner-a',
        payload: new ApimSubscriptionPayload(scope: '/apis', displayName: 'Partner A'),
    );

    expect($request->body()->all())->toBe([
        'properties' => [
            'scope' => '/apis',
            'displayName' => 'Partner A',
            'state' => 'active',
        ],
    ]);
});

it('sets if-match header on revoke patch', function (): void {
    $request = new UpdateApimSubscriptionState(
        subscriptionId: 'sub-1',
        resourceGroupName: 'rg-test',
        serviceName: 'my-apim',
        apimSubscriptionId: 'partner-a',
        payload: new GenericJsonPayload(['properties' => ['state' => 'suspended']]),
    );

    expect($request->headers()->all())->toHaveKey('If-Match', '*')
        ->and($request->body()->all())->toBe(['properties' => ['state' => 'suspended']]);
});
