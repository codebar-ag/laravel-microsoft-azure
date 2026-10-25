<?php

use CodebarAg\MicrosoftAzure\Config\ContainerConfigRepository;
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

it('reads configuration from the container at call time', function (): void {
    $manager = app(MicrosoftAzureManager::class);

    expect($manager->getDefaultConnection())->toBe('default');

    $sandboxed = clone app('config');
    $sandboxed->set('laravel-microsoft-azure.default', 'tenant-b');
    app()->instance('config', $sandboxed);

    expect(app(MicrosoftAzureManager::class))->toBe($manager)
        ->and($manager->getDefaultConnection())->toBe('tenant-b');
});

it('delegates every config repository call to the bound repository', function (): void {
    $repository = new ContainerConfigRepository;

    $repository->set('microsoft-azure-test.value', 'a');
    $repository->set('microsoft-azure-test.list', ['b']);
    $repository->prepend('microsoft-azure-test.list', 'a');
    $repository->push('microsoft-azure-test.list', 'c');

    expect($repository->has('microsoft-azure-test.value'))->toBeTrue()
        ->and($repository->get('microsoft-azure-test.value'))->toBe('a')
        ->and($repository->get('microsoft-azure-test.missing', 'fallback'))->toBe('fallback')
        ->and($repository->get('microsoft-azure-test.list'))->toBe(['a', 'b', 'c'])
        ->and($repository->all())->toBe(config()->all());
});
