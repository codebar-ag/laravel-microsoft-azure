<?php

use CodebarAg\MicrosoftAzure\Data\Arm\ResourceGroupData;
use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;

it('copies data objects with selective overrides', function (): void {
    $original = ResourceGroupData::fromAzure(resourceGroupFixture());

    $copy = $original->copyWith(name: 'rg-copy', provisioningState: ProvisioningState::Running);

    expect($copy)->toBeInstanceOf(AzureData::class)
        ->and($copy->name)->toBe('rg-copy')
        ->and($copy->location)->toBe($original->location)
        ->and($copy->provisioningState)->toBe(ProvisioningState::Running);
});

it('clones data objects that do not declare a constructor', function (): void {
    $data = new class extends AzureData {};

    $copy = $data->copyWith();

    expect($copy)->toBeInstanceOf(AzureData::class)
        ->and($copy)->not->toBe($data);
});
