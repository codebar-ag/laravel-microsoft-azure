<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\Tests\Support\LiveAzureTestContext;

it('can fetch a provisioned resource group', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $group = Azure::instance()
            ->resourceGroups($context->subscriptionId)
            ->get($context->resourceGroupName);

        expect($group->name)->toBe($context->resourceGroupName)
            ->and($group->location)->toBe($context->location);
    });
});

it('lists the provisioned resource group', function (): void {
    withLiveResourceGroup(function (LiveAzureTestContext $context): void {
        $groups = Azure::instance()
            ->resourceGroups($context->subscriptionId)
            ->list()
            ->pluck('name');

        expect($groups)->toContain($context->resourceGroupName);
    });
});
