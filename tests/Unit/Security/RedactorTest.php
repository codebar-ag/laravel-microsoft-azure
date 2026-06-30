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
