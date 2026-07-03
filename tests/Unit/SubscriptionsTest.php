<?php

use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionData;
use CodebarAg\MicrosoftAzure\Data\Payload\SubscriptionAliasPayload;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionState;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\CreateOrUpdateSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\SubscriptionAliases\GetSubscriptionAlias;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\CancelSubscription;
use CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions\ListSubscriptions;
use CodebarAg\MicrosoftAzure\Tests\Support\MicrosoftAzureFixture;
use Saloon\Http\Faking\MockResponse;

it('resolves subscription list endpoint', function (): void {
    expect((new ListSubscriptions)->resolveEndpoint())->toBe('/subscriptions');
});

it('resolves subscription cancel endpoint', function (): void {
    $request = new CancelSubscription('sub-123');

    expect($request->resolveEndpoint())
        ->toBe('/subscriptions/sub-123/providers/Microsoft.Subscription/cancel');
});

it('builds subscription alias create body', function (): void {
    $request = new CreateOrUpdateSubscriptionAlias(
        aliasName: 'tenant-acme',
        payload: new SubscriptionAliasPayload(
            billingScope: '/providers/Microsoft.Billing/billingAccounts/123/enrollmentAccounts/456',
            displayName: 'Acme Tenant',
            workload: SubscriptionWorkload::Production,
        ),
    );

    expect($request->body()->all())
        ->toHaveKey('properties.billingScope')
        ->toHaveKey('properties.displayName', 'Acme Tenant')
        ->toHaveKey('properties.workload', 'Production');
});

it('builds subscription alias bodies with optional subscription metadata', function (): void {
    $payload = new SubscriptionAliasPayload(
        billingScope: '/billing/scope',
        displayName: 'Acme Tenant',
        subscriptionId: '00000000-0000-0000-0000-000000000099',
        additionalProperties: ['managementGroupId' => 'mg-1'],
        tags: ['env' => 'prod'],
    );

    expect($payload->toAzureBody())->toMatchArray([
        'properties' => [
            'billingScope' => '/billing/scope',
            'displayName' => 'Acme Tenant',
            'workload' => 'Production',
            'subscriptionId' => '00000000-0000-0000-0000-000000000099',
            'additionalProperties' => [
                'managementGroupId' => 'mg-1',
                'tags' => ['env' => 'prod'],
            ],
        ],
    ]);
});

it('deserializes subscription data', function (): void {
    $data = SubscriptionData::fromAzure([
        'id' => '/subscriptions/sub-1',
        'subscriptionId' => 'sub-1',
        'displayName' => 'Production',
        'state' => 'Enabled',
    ]);

    expect($data->subscriptionId)->toBe('sub-1')
        ->and($data->state)->toBe(SubscriptionState::Enabled);
});

it('deserializes subscription alias data', function (): void {
    $data = SubscriptionAliasData::fromAzure(subscriptionAliasFixture());

    expect($data->name)->toBe('tenant-acme')
        ->and($data->provisioningState)->toBe(ProvisioningState::Accepted)
        ->and($data->subscriptionId)->not->toBeEmpty();
});

it('replays subscription alias fixture offline', function (): void {
    $client = clientWithMock([
        GetSubscriptionAlias::class => new MicrosoftAzureFixture('get-subscription-alias'),
    ]);

    $alias = $client->subscriptionAliases()->get('tenant-acme');

    expect($alias->provisioningState)->toBe(ProvisioningState::Accepted);
});

it('lists subscriptions from mock response', function (): void {
    $client = clientWithMock([
        ListSubscriptions::class => MockResponse::make(body: [
            'value' => [
                [
                    'id' => '/subscriptions/sub-1',
                    'subscriptionId' => 'sub-1',
                    'displayName' => 'Dev',
                    'state' => 'Enabled',
                ],
            ],
        ]),
    ]);

    $subscriptions = $client->subscriptions()->list();

    expect($subscriptions)->toHaveCount(1)
        ->and($subscriptions->first()?->displayName)->toBe('Dev');
});
