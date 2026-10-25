<?php

use CodebarAg\MicrosoftAzure\Facades\Azure;

dataset('host breaking names', [
    'path' => ['attacker.example/#'],
    'dot' => ['attacker.example'],
    'userinfo' => ['user@attacker'],
    'port' => ['attacker:443'],
    'query' => ['name?x='],
    'whitespace' => ['my app'],
    'empty' => [''],
    'leading hyphen' => ['-name'],
    'too long' => [str_repeat('a', 64)],
]);

it('rejects kudu app names that would leave the azure host', function (string $name): void {
    expect(fn () => Azure::instance()->kudu($name))
        ->toThrow(InvalidArgumentException::class, 'is not a valid host name label');
})->with('host breaking names');

it('rejects function app names that would leave the azure host', function (string $name): void {
    expect(fn () => Azure::instance()->functionRuntimeConnector($name, 'host-key'))
        ->toThrow(InvalidArgumentException::class, 'is not a valid host name label');
})->with('host breaking names');

it('rejects openai account names that would leave the azure host', function (string $name): void {
    expect(fn () => Azure::instance()->openAiConnector($name, 'api-key'))
        ->toThrow(InvalidArgumentException::class, 'is not a valid host name label');
})->with('host breaking names');

it('rejects foundry account names that would leave the azure host', function (string $name): void {
    expect(fn () => Azure::instance()->foundryConnector($name, 'project', 'api-key'))
        ->toThrow(InvalidArgumentException::class, 'is not a valid host name label');
})->with('host breaking names');

it('rejects storage account names that would leave the azure host', function (string $name): void {
    expect(fn () => Azure::instance()->storageQueue($name, base64_encode('key')))
        ->toThrow(InvalidArgumentException::class, 'is not a valid host name label');
})->with('host breaking names');

it('accepts regular azure resource names', function (): void {
    $client = Azure::instance();

    expect($client->kudu('my-app-staging')->resolveBaseUrl())->toBe('https://my-app-staging.scm.azurewebsites.net')
        ->and($client->functionRuntimeConnector('MyFunctionApp1', 'host-key')->resolveBaseUrl())->toBe('https://MyFunctionApp1.azurewebsites.net')
        ->and($client->openAiConnector('a', 'api-key')->resolveBaseUrl())->toBe('https://a.openai.azure.com')
        ->and($client->foundryConnector('account-1', 'project', 'api-key')->resolveBaseUrl())->toBe('https://account-1.services.ai.azure.com/api/projects/project')
        ->and($client->storageQueue(str_repeat('a', 24), base64_encode('key'))->resolveBaseUrl())->toBe('https://'.str_repeat('a', 24).'.queue.core.windows.net');
});
