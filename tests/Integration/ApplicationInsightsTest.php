<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;
use Illuminate\Support\Str;

it('creates a workspace-based Application Insights component', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $workspaceName = 'lma-law-'.Str::lower(Str::random(8));
        $componentName = 'lma-ai-'.Str::lower(Str::random(8));

        $workspaces = Azure::instance()->logAnalyticsWorkspaces($context->subscriptionId, $context->resourceGroupName);
        $workspace = $workspaces->workspace($workspaceName);

        $components = Azure::instance()->applicationInsights($context->subscriptionId, $context->resourceGroupName);
        $component = $components->component($componentName);

        try {
            $created = $workspaces->createOrUpdate($workspaceName, $context->location);

            $components->createOrUpdate(
                componentName: $componentName,
                location: $context->location,
                applicationType: 'web',
                kind: 'web',
                workspaceResourceId: $created->id,
                properties: [
                    'IngestionMode' => 'LogAnalytics',
                ],
            );

            $fetched = $component->get();

            expect($fetched->name)->toBe($componentName);
        } finally {
            try {
                $component->delete();
            } catch (Throwable) {
                // Best-effort cleanup; the resource group teardown is the safety net.
            }

            try {
                $workspace->delete();
            } catch (Throwable) {
                // Best-effort cleanup; the resource group teardown is the safety net.
            }
        }
    });
});
