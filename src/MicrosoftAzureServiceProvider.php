<?php

namespace CodebarAg\MicrosoftAzure;

use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\MicrosoftAzure\Transport\Auth\TokenRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MicrosoftAzureServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-microsoft-azure')
            ->hasConfigFile('laravel-microsoft-azure');
    }

    public function packageRegistered(): void
    {
        $this->app->bind(TokenRepository::class, EncryptedCacheTokenRepository::class);

        $this->app->singleton(MicrosoftAzureManager::class, function (Application $app): MicrosoftAzureManager {
            return new MicrosoftAzureManager(
                $app->make(ConfigRepository::class),
                $app->make(TokenRepository::class),
                $app->make(ClientCredentialsTokenFetcher::class),
            );
        });
        $this->app->alias(MicrosoftAzureManager::class, 'microsoft-azure.manager');
    }
}
