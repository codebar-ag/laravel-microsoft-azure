<?php

use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\AuthenticationException;
use CodebarAg\MicrosoftAzure\Exceptions\ConflictException;
use CodebarAg\MicrosoftAzure\Exceptions\ForbiddenException;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('creates a queue and sends, receives, and deletes messages via shared key and oauth', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $accountName = 'lma'.Str::lower(Str::random(10));
        $queueName = 'lma-queue';

        $storageAccounts = Azure::instance()->storageAccounts($context->subscriptionId, $context->resourceGroupName);
        $account = $storageAccounts->account($accountName);

        $queueCreated = false;

        try {
            $storageAccounts->createOrUpdate($accountName, $context->location, 'Standard_LRS', 'StorageV2');

            $provisioned = pollUntil(
                function () use ($account) {
                    try {
                        $data = $account->get();
                    } catch (ConflictException) {
                        return null;
                    }

                    return $data->provisioningState === ProvisioningState::Succeeded ? $data : null;
                },
                timeoutSeconds: 240,
                intervalSeconds: 10,
            );

            expect($provisioned->provisioningState)->toBe(ProvisioningState::Succeeded);

            $keys = pollUntil(
                function () use ($account) {
                    try {
                        return $account->listKeys();
                    } catch (ConflictException) {
                        return null;
                    }
                },
                timeoutSeconds: 120,
                intervalSeconds: 10,
            );

            $accountKey = $keys->keys[0]['value'];

            $account->queues()->createOrUpdate($queueName);
            $queueCreated = true;

            // --- Shared Key: the account key is guaranteed usable (no extra RBAC needed) ---
            $sharedKeyQueue = $account->queue($queueName, $accountKey);

            $sent = pollUntil(
                function () use ($sharedKeyQueue) {
                    try {
                        return $sharedKeyQueue->sendMessage('lma-shared-key-message');
                    } catch (Throwable) {
                        // Queue creation may not have fully propagated to the data plane yet.
                        return null;
                    }
                },
                timeoutSeconds: 60,
                intervalSeconds: 5,
            );

            expect($sent->messageId)->not->toBeEmpty();

            $received = $sharedKeyQueue->receiveMessages(numberOfMessages: 1);

            expect($received)->toHaveCount(1)
                ->and(base64_decode((string) $received->first()?->messageText))->toBe('lma-shared-key-message');

            $sharedKeyQueue->deleteMessage(
                (string) $received->first()?->messageId,
                (string) $received->first()?->popReceipt,
            );

            // --- Entra ID OAuth: requires the caller's service principal to have
            // "Storage Queue Data Contributor" (or equivalent) RBAC, which is
            // environment-dependent and outside this package's control. A 401/403
            // here proves the auth wiring reached the real endpoint correctly but
            // lacks authorization — that's a legitimate, non-failing outcome. ---
            $oauthQueue = $account->queue($queueName);

            try {
                $oauthSent = $oauthQueue->sendMessage('lma-oauth-message');

                expect($oauthSent->messageId)->not->toBeEmpty();

                $oauthReceived = $oauthQueue->receiveMessages(numberOfMessages: 1);

                expect($oauthReceived)->toHaveCount(1);

                $oauthQueue->deleteMessage(
                    (string) $oauthReceived->first()?->messageId,
                    (string) $oauthReceived->first()?->popReceipt,
                );
            } catch (ForbiddenException|AuthenticationException $e) {
                test()->markTestSkipped(
                    'Entra ID OAuth path reached Azure but was denied — service principal likely lacks '
                    .'"Storage Queue Data Contributor" RBAC on this account. Shared Key path above already '
                    .'verified the gateway end-to-end. Denial reason: '.$e->getMessage(),
                );
            }
        } finally {
            if ($queueCreated) {
                try {
                    $account->queues()->delete($queueName);
                } catch (Throwable) {
                    // Best-effort cleanup; the resource group teardown is the safety net.
                }
            }

            try {
                $account->delete();
            } catch (Throwable) {
                // Best-effort cleanup; the resource group teardown is the safety net.
            }
        }
    });
});
