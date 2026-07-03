<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveSubscriptionAliasTestContext;

it('lists subscriptions and includes the configured subscription', function (): void {
    runAzureIntegration(function (): void {
        $subscriptionId = integrationSubscriptionId();

        $subscriptions = Azure::instance()->subscriptions()->list()->pluck('subscriptionId');

        if ($subscriptions->contains($subscriptionId)) {
            expect($subscriptions)->toContain($subscriptionId);

            return;
        }

        expect(Azure::instance()->subscriptions()->get($subscriptionId)->subscriptionId)
            ->toBe($subscriptionId);
    });
});

it('gets the configured subscription by id', function (): void {
    runAzureIntegration(function (): void {
        $subscriptionId = integrationSubscriptionId();

        $subscription = Azure::instance()->subscriptions()->get($subscriptionId);

        expect($subscription->subscriptionId)->toBe($subscriptionId);
    });
});

it('creates a subscription alias and polls until provisioned', function (): void {
    withLiveSubscriptionAlias(function (LiveSubscriptionAliasTestContext $context): void {
        expect($context->alias->provisioningState?->isTerminal())->toBeTrue()
            ->and($context->subscriptionId())->not->toBeEmpty();
    });
});

it('updates alias display name via createOrUpdate', function (): void {
    withLiveSubscriptionAlias(function (LiveSubscriptionAliasTestContext $context): void {
        $updatedDisplayName = 'laravel-microsoft-azure integration test updated';

        Azure::instance()->subscriptionAliases()->createOrUpdate(
            aliasName: $context->aliasName,
            billingScope: $context->billingScope,
            displayName: $updatedDisplayName,
        );

        $alias = pollSubscriptionAlias($context->aliasName);

        expect($alias->displayName)->toBe($updatedDisplayName);
    });
});

it('lists subscription aliases and includes the test alias', function (): void {
    withLiveSubscriptionAlias(function (LiveSubscriptionAliasTestContext $context): void {
        $aliases = Azure::instance()->subscriptionAliases()->list()->pluck('name');

        expect($aliases)->toContain($context->aliasName);
    });
});

it('gets the alias by name', function (): void {
    withLiveSubscriptionAlias(function (LiveSubscriptionAliasTestContext $context): void {
        $alias = Azure::instance()->subscriptionAliases()->get($context->aliasName);

        expect($alias->name)->toBe($context->aliasName)
            ->and($alias->billingScope)->toBe($context->billingScope);
    });
});

it('cancels a provisioned subscription via REST', function (): void {
    withLiveSubscriptionAlias(function (LiveSubscriptionAliasTestContext $context): void {
        $subscriptionId = $context->subscriptionId();

        expect($subscriptionId)->not->toBeEmpty();

        $canceled = Azure::instance()->subscriptions()->cancel($subscriptionId);

        expect($canceled->subscriptionId)->toBe($subscriptionId);
    });
});
