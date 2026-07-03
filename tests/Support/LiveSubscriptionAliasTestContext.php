<?php

namespace CodebarAg\MicrosoftAzure\Tests\Support;

use CodebarAg\MicrosoftAzure\Data\Arm\SubscriptionAliasData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Enums\SubscriptionWorkload;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use Illuminate\Support\Str;
use Throwable;

final class LiveSubscriptionAliasTestContext
{
    private bool $teardownAttempted = false;

    public function __construct(
        public readonly string $aliasName,
        public readonly string $billingScope,
        public SubscriptionAliasData $alias,
    ) {}

    public static function provision(?string $billingScope = null): self
    {
        $billingScope ??= integrationBillingScope();

        $aliasName = 'lma-test-'.Str::lower(Str::random(12));

        $alias = Azure::instance()->subscriptionAliases()->createOrUpdate(
            aliasName: $aliasName,
            billingScope: $billingScope,
            displayName: 'laravel-microsoft-azure integration test',
            workload: SubscriptionWorkload::DevTest,
            tags: [
                'purpose' => 'laravel-microsoft-azure-integration-test',
                'managed-by' => 'laravel-microsoft-azure',
            ],
        );

        $alias = pollSubscriptionAlias($aliasName);

        if ($alias->provisioningState === ProvisioningState::Failed) {
            throw new \RuntimeException(
                "Subscription alias [{$aliasName}] provisioning failed."
            );
        }

        return new self($aliasName, $billingScope, $alias);
    }

    public function subscriptionId(): ?string
    {
        return $this->alias->subscriptionId;
    }

    public function teardown(): void
    {
        if ($this->teardownAttempted) {
            return;
        }

        $this->teardownAttempted = true;

        $subscriptionId = $this->subscriptionId();

        if (filled($subscriptionId)) {
            try {
                Azure::instance()->subscriptions()->cancel($subscriptionId);
            } catch (Throwable) {
                // Best-effort cleanup; cancel may already be in progress.
            }
        }

        try {
            Azure::instance()->subscriptionAliases()->delete($this->aliasName);
        } catch (Throwable) {
            // Best-effort cleanup; alias may already be deleted.
        }
    }
}
