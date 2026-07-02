<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('creates a vault, manages a secret, and purges the deleted vault', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $vaultName = 'lma-kv-'.Str::lower(Str::random(8));
        $tenantId = (string) env('MICROSOFT_AZURE_TENANT_ID');
        $clientId = (string) env('MICROSOFT_AZURE_CLIENT_ID');

        $servicePrincipal = Azure::graph()->servicePrincipals()->findByAppIdOrFail($clientId);

        $vaults = Azure::instance()->vaults($context->subscriptionId, $context->resourceGroupName);
        $vault = $vaults->vault($vaultName);

        $vaultCreated = false;

        try {
            $vaults->createOrUpdate(
                vaultName: $vaultName,
                location: $context->location,
                tenantId: $tenantId,
                enableRbacAuthorization: false,
                properties: [
                    'accessPolicies' => [
                        [
                            'tenantId' => $tenantId,
                            'objectId' => $servicePrincipal->id,
                            'permissions' => [
                                'secrets' => ['get', 'set', 'delete', 'list'],
                            ],
                        ],
                    ],
                ],
            );
            $vaultCreated = true;

            $secrets = Azure::instance()->secrets($vaultName);

            $secrets->set('lma-test-secret', 'lma-test-value');

            $secret = $secrets->get('lma-test-secret');

            expect($secret->value)->toBe('lma-test-value');

            $secrets->delete('lma-test-secret');

            $vault->delete();
            $vaultCreated = false;
        } finally {
            if ($vaultCreated) {
                try {
                    $vault->delete();
                } catch (Throwable) {
                    // Best-effort cleanup; the resource group teardown is the safety net.
                }
            }

            try {
                Azure::instance()->deletedVaults($context->subscriptionId)->purge($context->location, $vaultName);
            } catch (Throwable) {
                // Tolerate 404 (already purged), 409 (still deleting), and 403 (purge protection).
            }
        }
    });
});
