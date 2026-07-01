<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;

it('binds token repository and manager services', function (): void {
    expect(app(TokenRepository::class))->toBeInstanceOf(EncryptedCacheTokenRepository::class)
        ->and(app('microsoft-azure.manager'))->toBe(app(MicrosoftAzureManager::class))
        ->and(Azure::getFacadeRoot())->toBeInstanceOf(MicrosoftAzureManager::class);
});

it('caches default connection instances until forgotten', function (): void {
    $manager = app(MicrosoftAzureManager::class);

    $first = $manager->instance('default');
    $second = $manager->instance('default');

    expect($first)->toBe($second);

    $manager->forget('default');

    expect($manager->instance('default'))->not->toBe($first);
});

it('throws when resolving unknown connection names', function (): void {
    expect(fn () => app(MicrosoftAzureManager::class)->resolveConfig('missing'))
        ->toThrow(InvalidArgumentException::class);
});

it('clears all cached clients when forget is called without a name', function (): void {
    $manager = app(MicrosoftAzureManager::class);
    $manager->instance('default');
    $manager->forget();

    expect(true)->toBeTrue();
});
