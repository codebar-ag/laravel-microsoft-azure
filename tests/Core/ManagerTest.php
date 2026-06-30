<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;

it('registers the manager singleton', function (): void {
    expect(app(MicrosoftAzureManager::class))->toBeInstanceOf(MicrosoftAzureManager::class)
        ->and(app('microsoft-azure.manager'))->toBeInstanceOf(MicrosoftAzureManager::class);
});

it('resolves the default connection from config', function (): void {
    $client = Azure::instance();

    expect($client->name())->toBe('default')
        ->and($client->config->subscriptionId)->toBe('00000000-0000-0000-0000-000000000003');
});

it('builds runtime connections without name caching collisions', function (): void {
    $manager = app(MicrosoftAzureManager::class);

    $a = $manager->connection(testConnectionConfig());
    $b = $manager->connection(testConnectionConfig());

    expect($a)->not->toBe($b)
        ->and($a->config->identifier())->toBe($b->config->identifier());
});
