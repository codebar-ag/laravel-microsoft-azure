<?php

use CodebarAg\MicrosoftAzure\Security\Redactor;

it('redacts bearer tokens in strings', function (): void {
    $redactor = new Redactor;

    expect($redactor->string('Authorization: Bearer eyJ.secret.token'))
        ->toBe('Authorization: Bearer [REDACTED]');
});

it('redacts secret keys in nested arrays', function (): void {
    $redactor = new Redactor;

    $result = $redactor->redact([
        'Authorization' => 'Bearer secret',
        'client_secret' => 'abc',
        'nested' => ['access_token' => 'token-value'],
        'safe' => 'visible',
    ]);

    expect($result)->toMatchArray([
        'Authorization' => '[REDACTED]',
        'client_secret' => '[REDACTED]',
        'nested' => ['access_token' => '[REDACTED]'],
        'safe' => 'visible',
    ]);
});

it('redacts json secret fields in strings', function (): void {
    $redactor = new Redactor;

    expect($redactor->string('{"access_token":"super-secret"}'))
        ->toBe('{"access_token":"[REDACTED]"}');
});

it('returns the original value for non-string scalars', function (): void {
    $redactor = new Redactor;

    expect($redactor->redact(42))->toBe(42);
});

it('redacts nested arrays via redactArray', function (): void {
    $redactor = new Redactor;

    expect($redactor->redactArray([
        'authorization' => 'Bearer secret',
        'nested' => ['client_secret' => 'value'],
    ]))->toBe([
        'authorization' => '[REDACTED]',
        'nested' => ['client_secret' => '[REDACTED]'],
    ]);
});

it('redacts azure account keys and api keys in json strings', function (): void {
    $redactor = new Redactor;

    $body = '{"key1":"cog-1","key2":"cog-2","primaryKey":"apim-1","secondaryKey":"apim-2",'
        .'"primarySharedKey":"law-1","masterKey":"func-1","credentials":{"type":"ApiKey","key":"foundry-1"},'
        .'"connectionString":"Endpoint=sb://x/;SharedAccessKey=abc","keyName":"key1","name":"visible"}';

    $redacted = $redactor->string($body);

    expect($redacted)
        ->not->toContain('cog-1')
        ->not->toContain('cog-2')
        ->not->toContain('apim-1')
        ->not->toContain('apim-2')
        ->not->toContain('law-1')
        ->not->toContain('func-1')
        ->not->toContain('foundry-1')
        ->not->toContain('SharedAccessKey=abc')
        ->toContain('"keyName":"key1"')
        ->toContain('"name":"visible"');
});

it('redacts sas signatures and storage account keys in query and connection strings', function (): void {
    $redactor = new Redactor;

    expect($redactor->string('https://acc.blob.core.windows.net/c?sv=2024-01-01&sig=abc%2Bdef&se=2026'))
        ->toBe('https://acc.blob.core.windows.net/c?sv=2024-01-01&sig=[REDACTED]&se=2026')
        ->and($redactor->string('DefaultEndpointsProtocol=https;AccountName=acc;AccountKey=c2VjcmV0'))
        ->toBe('DefaultEndpointsProtocol=https;AccountName=acc;AccountKey=[REDACTED]');
});

it('redacts api key headers in arrays', function (): void {
    $redactor = new Redactor;

    expect($redactor->redactArray([
        'api-key' => 'secret',
        'x-functions-key' => 'secret',
        'Ocp-Apim-Subscription-Key' => 'secret',
        'x-ms-request-id' => 'visible',
    ]))->toBe([
        'api-key' => '[REDACTED]',
        'x-functions-key' => '[REDACTED]',
        'Ocp-Apim-Subscription-Key' => '[REDACTED]',
        'x-ms-request-id' => 'visible',
    ]);
});
