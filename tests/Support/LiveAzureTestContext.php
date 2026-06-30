<?php

namespace CodebarAg\MicrosoftAzure\Tests\Support;

use CodebarAg\MicrosoftAzure\Facades\Azure;
use Illuminate\Support\Str;
use Throwable;

final class LiveAzureTestContext
{
    private bool $teardownAttempted = false;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $location,
    ) {}

    public static function provisionResourceGroup(?string $subscriptionId = null, ?string $location = null): self
    {
        $subscriptionId ??= integrationSubscriptionId();
        $location ??= integrationLocation();

        $resourceGroupName = 'rg-lma-'.Str::lower(Str::random(12));

        Azure::instance()->resourceGroups($subscriptionId)->createOrUpdate(
            $resourceGroupName,
            $location,
            [
                'tags' => [
                    'purpose' => 'laravel-microsoft-azure-integration-test',
                    'managed-by' => 'laravel-microsoft-azure',
                ],
            ],
        );

        return new self($subscriptionId, $resourceGroupName, $location);
    }

    public function teardown(): void
    {
        if ($this->teardownAttempted) {
            return;
        }

        $this->teardownAttempted = true;

        try {
            Azure::instance()->resourceGroups($this->subscriptionId)->delete($this->resourceGroupName);
        } catch (Throwable) {
            // Best-effort cleanup; Azure delete is async and may already be gone.
        }
    }
}
