<?php

use CodebarAg\MicrosoftAzure\Transport\ArmConnector;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\MicrosoftAzure\Transport\GraphConnector;
use CodebarAg\MicrosoftAzure\Transport\KeyVaultConnector;
use CodebarAg\MicrosoftAzure\Transport\KuduConnector;

it('resolves connector base urls and default config', function (): void {
    $config = testConnectionConfig();
    $tokens = new EncryptedCacheTokenRepository;
    $fetcher = new ClientCredentialsTokenFetcher;

    $arm = new ArmConnector($config, $tokens, $fetcher);
    $graph = new GraphConnector($config, $tokens, $fetcher);
    $vault = new KeyVaultConnector($config, $tokens, $fetcher, 'myvault.vault.azure.net');
    $kudu = new KuduConnector($config, $tokens, $fetcher, 'my-app');

    expect($arm->resolveBaseUrl())->toBe('https://management.azure.com')
        ->and($graph->resolveBaseUrl())->toBe('https://graph.microsoft.com/v1.0')
        ->and($vault->resolveBaseUrl())->toBe('https://myvault.vault.azure.net')
        ->and($kudu->resolveBaseUrl())->toBe('https://my-app.scm.azurewebsites.net')
        ->and($arm->defaultHeaders())->toBe(['Accept' => 'application/json'])
        ->and($arm->defaultConfig())->toBe(['timeout' => 30]);
});

it('memoizes connectors on the azure client', function (): void {
    $client = clientWithSeededToken();

    expect($client->arm())->toBe($client->arm())
        ->and($client->graph())->toBe($client->graph())
        ->and($client->keyVault('vault.vault.azure.net'))->toBe($client->keyVault('vault.vault.azure.net'))
        ->and($client->kudu('my-app'))->toBe($client->kudu('my-app'));
});
