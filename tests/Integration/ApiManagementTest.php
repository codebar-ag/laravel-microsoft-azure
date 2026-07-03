<?php

use CodebarAg\MicrosoftAzure\Enums\ApimSubscriptionState;
use CodebarAg\MicrosoftAzure\Exceptions\ConflictException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('provisions an api management service and manages a subscription end to end', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $serviceName = 'lma-apim-'.Str::lower(Str::random(8));
        $subscriptionId = 'lma-partner-a';

        $gateway = Azure::instance()->apiManagement($context->subscriptionId, $context->resourceGroupName);
        $service = $gateway->service($serviceName);

        $serviceCreated = false;

        try {
            $gateway->createOrUpdate(
                serviceName: $serviceName,
                location: $context->location,
                publisherEmail: 'lma-test@example.com',
                publisherName: 'LMA Integration Test',
                skuName: 'Consumption',
                skuCapacity: 0,
            );
            $serviceCreated = true;

            // Consumption-tier APIM provisioning commonly takes 5-15 minutes.
            $provisioned = pollUntil(
                function () use ($service) {
                    try {
                        $data = $service->get();
                    } catch (NotFoundException|ConflictException) {
                        return null;
                    }

                    return $data->provisioningState === 'Succeeded' ? $data : null;
                },
                timeoutSeconds: 1500,
                intervalSeconds: 20,
            );

            expect($provisioned->name)->toBe($serviceName)
                ->and($provisioned->provisioningState)->toBe('Succeeded')
                ->and($provisioned->gatewayUrl)->not->toBeEmpty();

            $subscriptions = $service->subscriptions();

            $created = $subscriptions->create($subscriptionId, 'Partner A');

            expect($created->name)->toBe($subscriptionId)
                ->and($created->displayName)->toBe('Partner A');

            $fetched = $subscriptions->get($subscriptionId);

            expect($fetched->name)->toBe($subscriptionId);

            $list = $subscriptions->list();

            expect($list->pluck('name')->all())->toContain($subscriptionId);

            $subscription = $subscriptions->subscription($subscriptionId);

            // Keys are never returned on create/get — only via listSecrets().
            $keys = $subscription->listSecrets();

            expect($keys->primaryKey)->not->toBeEmpty()
                ->and($keys->secondaryKey)->not->toBeEmpty();

            $subscription->regeneratePrimaryKey();

            $rotatedKeys = $subscription->listSecrets();

            expect($rotatedKeys->primaryKey)->not->toBe($keys->primaryKey)
                ->and($rotatedKeys->secondaryKey)->toBe($keys->secondaryKey);

            $subscription->regenerateSecondaryKey();

            $bothRotatedKeys = $subscription->listSecrets();

            expect($bothRotatedKeys->secondaryKey)->not->toBe($keys->secondaryKey);

            // Verifies the PATCH .../subscriptions/{sid} with `If-Match: *` design
            // decision (flagged as unverified against a live tenant during planning).
            $subscription->revoke();

            $revoked = $subscriptions->get($subscriptionId);

            expect($revoked->state)->toBe(ApimSubscriptionState::Suspended);
        } finally {
            if ($serviceCreated) {
                try {
                    $service->delete();
                } catch (Throwable) {
                    // Best-effort cleanup; the resource group teardown is the safety net.
                }
            }
        }
    });
})->group('slow');
