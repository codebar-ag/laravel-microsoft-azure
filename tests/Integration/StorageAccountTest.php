<?php

use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\ConflictException;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('creates a storage account, lists keys, and creates a blob container', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $accountName = 'lma'.Str::lower(Str::random(10));

        $storageAccounts = Azure::instance()->storageAccounts($context->subscriptionId, $context->resourceGroupName);
        $account = $storageAccounts->account($accountName);

        try {
            $storageAccounts->createOrUpdate($accountName, $context->location, 'Standard_LRS', 'StorageV2');

            $provisioned = pollUntil(
                function () use ($account) {
                    try {
                        $data = $account->get();
                    } catch (ConflictException) {
                        // The Storage RP holds an exclusive lock while creation finalizes.
                        return null;
                    }

                    return $data->provisioningState === ProvisioningState::Succeeded ? $data : null;
                },
                timeoutSeconds: 240,
                intervalSeconds: 10,
            );

            expect($provisioned->name)->toBe($accountName)
                ->and($provisioned->provisioningState)->toBe(ProvisioningState::Succeeded);

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

            expect($keys->keys)->not->toBeEmpty();

            $container = pollUntil(
                function () use ($account) {
                    try {
                        return $account->blobContainers()->createOrUpdate('lma-container');
                    } catch (ConflictException) {
                        return null;
                    }
                },
                timeoutSeconds: 120,
                intervalSeconds: 10,
            );

            expect($container->name)->toBe('lma-container');
        } finally {
            try {
                $account->delete();
            } catch (Throwable) {
                // Best-effort cleanup; the resource group teardown is the safety net.
            }
        }
    });
});
