<?php

use CodebarAg\MicrosoftAzure\Transport\ArmConnector;
use CodebarAg\MicrosoftAzure\Transport\Auth\ClientCredentialsTokenFetcher;
use CodebarAg\MicrosoftAzure\Transport\Auth\EncryptedCacheTokenRepository;
use CodebarAg\MicrosoftAzure\Transport\FoundryConnector;
use CodebarAg\MicrosoftAzure\Transport\FunctionRuntimeConnector;
use CodebarAg\MicrosoftAzure\Transport\GraphConnector;
use CodebarAg\MicrosoftAzure\Transport\KeyVaultConnector;
use CodebarAg\MicrosoftAzure\Transport\KuduConnector;
use CodebarAg\MicrosoftAzure\Transport\LogAnalyticsConnector;
use CodebarAg\MicrosoftAzure\Transport\OpenAiConnector;

it('resolves connector base urls and default config', function (): void {
    $config = testConnectionConfig();
    $tokens = new EncryptedCacheTokenRepository;
    $fetcher = new ClientCredentialsTokenFetcher;

    $arm = new ArmConnector($config, $tokens, $fetcher);
    $graph = new GraphConnector($config, $tokens, $fetcher);
    $vault = new KeyVaultConnector($config, $tokens, $fetcher, 'myvault.vault.azure.net');
    $kudu = new KuduConnector($config, $tokens, $fetcher, 'my-app');
    $openAi = new OpenAiConnector($config, $tokens, $fetcher, 'my-openai');
    $foundry = new FoundryConnector($config, $tokens, $fetcher, 'my-foundry', 'default');
    $runtime = new FunctionRuntimeConnector($config, $tokens, $fetcher, 'my-app');
    $logAnalytics = new LogAnalyticsConnector($config, $tokens, $fetcher);

    expect($arm->resolveBaseUrl())->toBe('https://management.azure.com')
        ->and($graph->resolveBaseUrl())->toBe('https://graph.microsoft.com/v1.0')
        ->and($vault->resolveBaseUrl())->toBe('https://myvault.vault.azure.net')
        ->and($kudu->resolveBaseUrl())->toBe('https://my-app.scm.azurewebsites.net')
        ->and($openAi->resolveBaseUrl())->toBe('https://my-openai.openai.azure.com')
        ->and($foundry->resolveBaseUrl())->toBe('https://my-foundry.services.ai.azure.com/api/projects/default')
        ->and($runtime->resolveBaseUrl())->toBe('https://my-app.azurewebsites.net')
        ->and($logAnalytics->resolveBaseUrl())->toBe('https://api.loganalytics.azure.com/v1')
        ->and($arm->defaultHeaders())->toBe(['Accept' => 'application/json'])
        ->and($arm->defaultConfig())->toBe(['timeout' => 30]);
});

it('memoizes connectors on the azure client', function (): void {
    $client = clientWithSeededToken();

    expect($client->arm())->toBe($client->arm())
        ->and($client->graph())->toBe($client->graph())
        ->and($client->keyVault('vault.vault.azure.net'))->toBe($client->keyVault('vault.vault.azure.net'))
        ->and($client->kudu('my-app'))->toBe($client->kudu('my-app'))
        ->and($client->openAiConnector('my-openai'))->toBe($client->openAiConnector('my-openai'))
        ->and($client->foundryConnector('my-foundry', 'default'))->toBe($client->foundryConnector('my-foundry', 'default'))
        ->and($client->functionRuntimeConnector('my-app'))->toBe($client->functionRuntimeConnector('my-app'));
});
