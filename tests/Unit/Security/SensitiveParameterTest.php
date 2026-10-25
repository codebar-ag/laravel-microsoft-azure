<?php

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\Data\Payload\ClientCredentialsTokenPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\SetSecretPayload;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;
use CodebarAg\MicrosoftAzure\Resources\FunctionAppHostKeysResource;
use CodebarAg\MicrosoftAzure\Resources\FunctionKeysResource;
use CodebarAg\MicrosoftAzure\Resources\SecretsResource;
use CodebarAg\MicrosoftAzure\Resources\StorageAccountResource;
use CodebarAg\MicrosoftAzure\Transport\Auth\SharedKeyAuthenticator;
use CodebarAg\MicrosoftAzure\Transport\FoundryConnector;
use CodebarAg\MicrosoftAzure\Transport\FunctionRuntimeConnector;
use CodebarAg\MicrosoftAzure\Transport\OpenAiConnector;
use CodebarAg\MicrosoftAzure\Transport\StorageQueueConnector;

dataset('secret parameters', [
    [ConnectionConfig::class, '__construct', 'clientSecret'],
    [ConnectionConfig::class, 'make', 'attrs'],
    [ConnectionConfig::class, 'for', 'attrs'],
    [ClientCredentialsTokenPayload::class, '__construct', 'clientSecret'],
    [SetSecretPayload::class, '__construct', 'value'],
    [FunctionKeyPayload::class, '__construct', 'value'],
    [SharedKeyAuthenticator::class, '__construct', 'accountKey'],
    [OpenAiConnector::class, '__construct', 'apiKey'],
    [FoundryConnector::class, '__construct', 'apiKey'],
    [FunctionRuntimeConnector::class, '__construct', 'hostKey'],
    [StorageQueueConnector::class, '__construct', 'accountKey'],
    [AzureClient::class, 'openAiConnector', 'apiKey'],
    [AzureClient::class, 'foundryConnector', 'apiKey'],
    [AzureClient::class, 'functionRuntimeConnector', 'hostKey'],
    [AzureClient::class, 'storageQueue', 'accountKey'],
    [MicrosoftAzureManager::class, 'openAi', 'apiKey'],
    [MicrosoftAzureManager::class, 'foundry', 'apiKey'],
    [MicrosoftAzureManager::class, 'functionRuntime', 'hostKey'],
    [SecretsResource::class, 'set', 'value'],
    [FunctionAppHostKeysResource::class, 'createOrUpdate', 'value'],
    [FunctionKeysResource::class, 'createOrUpdate', 'value'],
    [StorageAccountResource::class, 'queue', 'accountKey'],
]);

it('marks secret parameters as sensitive', function (string $class, string $method, string $parameter): void {
    $reflection = collect((new ReflectionMethod($class, $method))->getParameters())
        ->firstOrFail(fn (ReflectionParameter $p): bool => $p->getName() === $parameter);

    expect($reflection->getAttributes(SensitiveParameter::class))->toHaveCount(1);
})->with('secret parameters');

it('keeps the client secret out of exception stack traces', function (): void {
    try {
        ConnectionConfig::make('default', ['clientSecret' => 'super-secret-value']);
    } catch (InvalidArgumentException $exception) {
        $frame = collect($exception->getTrace())
            ->firstOrFail(fn (array $frame): bool => ($frame['class'] ?? null) === ConnectionConfig::class && $frame['function'] === 'make');

        expect($frame['args'][1] ?? null)->toBeInstanceOf(SensitiveParameterValue::class)
            ->and($exception->getTraceAsString())->not->toContain('super-secret-value');

        return;
    }

    test()->fail('Expected an InvalidArgumentException.');
});
