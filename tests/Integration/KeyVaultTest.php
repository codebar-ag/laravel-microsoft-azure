<?php

use CodebarAg\MicrosoftAzure\Data\KeyVault\SecretData;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;
use Saloon\Exceptions\Request\FatalRequestException;

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

            // The vault's data-plane hostname (https://{vault}.vault.azure.net) can take
            // a little while to become DNS-resolvable right after ARM creates the vault,
            // so retry the first data-plane call rather than failing on a fresh vault.
            pollUntil(
                function () use ($secrets): ?SecretData {
                    try {
                        return $secrets->set('lma-test-secret', 'lma-test-value');
                    } catch (FatalRequestException) {
                        return null;
                    }
                },
                timeoutSeconds: 60,
                intervalSeconds: 5,
            );

            // Key Vault secret reads can lag slightly behind a just-completed write
            // (read-after-write eventual consistency), so retry briefly instead of
            // mocking this call — mocking it collided with the "get-secret" fixture
            // committed for tests/Unit/Data/DtoDeserializationTest.php's offline test.
            $secret = pollUntil(
                function () use ($secrets): ?SecretData {
                    $secret = $secrets->get('lma-test-secret');

                    return $secret->value === 'lma-test-value' ? $secret : null;
                },
                timeoutSeconds: 30,
                intervalSeconds: 3,
            );

            expect($secret->value)->toBe('lma-test-value');

            $secrets->set('lma-test-secret', 'lma-test-value-2');

            $versions = $secrets->versions('lma-test-secret');

            expect($versions)->toHaveCount(2)
                ->and($versions->pluck('enabled')->unique()->all())->toBe([true]);

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
