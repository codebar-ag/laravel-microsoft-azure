<?php

namespace CodebarAg\MicrosoftAzure\Tests;

use CodebarAg\MicrosoftAzure\MicrosoftAzureServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\MockConfig;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
            MicrosoftAzureServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('laravel-microsoft-azure.connections.default', [
            'tenant_id' => '00000000-0000-0000-0000-000000000001',
            'client_id' => '00000000-0000-0000-0000-000000000002',
            'client_secret' => 'test-secret',
            'subscription_id' => '00000000-0000-0000-0000-000000000003',
            'cache_driver' => 'array',
            'cache_lifetime_in_seconds' => 3600,
            'request_timeout_in_seconds' => 30,
        ]);

        MockConfig::setFixturePath(__DIR__.'/Fixtures/saloon');
    }
}
