<?php

use CodebarAg\MicrosoftAzure\Data\LogAnalytics\QueryResultsData;
use CodebarAg\MicrosoftAzure\Exceptions\BadRequestException;
use CodebarAg\MicrosoftAzure\Exceptions\NotFoundException;
use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('creates a workspace and queries it via the Log Analytics data plane', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $workspaceName = 'lma-law-'.Str::lower(Str::random(8));

        $workspaces = Azure::instance()->logAnalyticsWorkspaces($context->subscriptionId, $context->resourceGroupName);
        $workspace = $workspaces->workspace($workspaceName);

        try {
            $workspaces->createOrUpdate($workspaceName, $context->location);

            $customerId = pollUntil(
                fn () => $workspace->get()->customerId,
                timeoutSeconds: 180,
                intervalSeconds: 10,
            );

            expect($customerId)->not->toBeEmpty();

            $results = pollUntil(
                function () use ($customerId) {
                    try {
                        return Azure::instance()->logAnalytics()->query($customerId, 'Usage | take 1', 'P1D');
                    } catch (NotFoundException|BadRequestException) {
                        // The query endpoint may 404/400 briefly right after workspace creation.
                        return null;
                    }
                },
                timeoutSeconds: 120,
                intervalSeconds: 10,
            );

            expect($results)->toBeInstanceOf(QueryResultsData::class)
                ->and($results->tables)->toBeArray();
        } finally {
            try {
                $workspace->delete();
            } catch (Throwable) {
                // Best-effort cleanup; the resource group teardown is the safety net.
            }
        }
    });
});
