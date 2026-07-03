<?php

use CodebarAg\MicrosoftAzure\Requests\StorageQueue\ReceiveMessages;
use CodebarAg\MicrosoftAzure\Transport\Auth\SharedKeyAuthenticator;

$accountKey = 'TXlTdXBlclNlY3JldFN0b3JhZ2VBY2NvdW50S2V5MQ=='; // base64("MySuperSecretStorageAccountKey1")

it('signs a GET request with query parameters and no body (known-answer vector)', function () use ($accountKey): void {
    $authenticator = new SharedKeyAuthenticator('myaccount', $accountKey);

    $signature = $authenticator->sign(
        method: 'GET',
        headers: [
            'x-ms-date' => 'Tue, 01 Jan 2026 00:00:00 GMT',
            'x-ms-version' => '2025-05-05',
        ],
        path: '/myqueue/messages',
        query: ['numofmessages' => '5', 'visibilitytimeout' => '30'],
        body: '',
    );

    // Independently computed via `openssl dgst -sha256 -mac HMAC` against the
    // documented Shared Key string-to-sign algorithm — not derived from this class.
    expect($signature)->toBe('dX4t0XogXyI0uOOEGKBRHeJ4YfMhq3yBrG9bTEiK/S8=');
});

it('signs a POST request with a body and Content-Type (known-answer vector)', function () use ($accountKey): void {
    $authenticator = new SharedKeyAuthenticator('myaccount', $accountKey);

    $body = '<QueueMessage><MessageText>aGVsbG8=</MessageText></QueueMessage>';

    $signature = $authenticator->sign(
        method: 'POST',
        headers: [
            'x-ms-date' => 'Tue, 01 Jan 2026 00:00:00 GMT',
            'x-ms-version' => '2025-05-05',
            'Content-Type' => 'application/xml',
        ],
        path: '/myqueue/messages',
        query: [],
        body: $body,
    );

    expect($signature)->toBe('AQShas9LUUTQwV8jM3ms+9eqSazdxGspPB6Jv/sZa7c=');
});

it('sorts x-ms-* headers and query parameters when canonicalizing', function () use ($accountKey): void {
    $authenticator = new SharedKeyAuthenticator('myaccount', $accountKey);

    $inOrder = $authenticator->sign(
        method: 'GET',
        headers: ['x-ms-version' => '2025-05-05', 'x-ms-date' => 'Tue, 01 Jan 2026 00:00:00 GMT'],
        path: '/myqueue/messages',
        query: ['visibilitytimeout' => '30', 'numofmessages' => '5'],
        body: '',
    );

    expect($inOrder)->toBe('dX4t0XogXyI0uOOEGKBRHeJ4YfMhq3yBrG9bTEiK/S8=');
});

it('ignores non x-ms headers when canonicalizing', function () use ($accountKey): void {
    $authenticator = new SharedKeyAuthenticator('myaccount', $accountKey);

    $signature = $authenticator->sign(
        method: 'GET',
        headers: [
            'x-ms-date' => 'Tue, 01 Jan 2026 00:00:00 GMT',
            'x-ms-version' => '2025-05-05',
            'Accept' => 'application/xml',
        ],
        path: '/myqueue/messages',
        query: ['numofmessages' => '5', 'visibilitytimeout' => '30'],
        body: '',
    );

    expect($signature)->toBe('dX4t0XogXyI0uOOEGKBRHeJ4YfMhq3yBrG9bTEiK/S8=');
});

it('sets x-ms-date and Authorization headers when authenticating a pending request', function () use ($accountKey): void {
    $client = clientWithSeededToken();

    $connector = $client->storageQueue('myaccount', $accountKey);

    $request = new ReceiveMessages('myqueue');
    $pendingRequest = $connector->createPendingRequest($request);

    expect($pendingRequest->headers()->get('x-ms-date'))->toBeString()
        ->and($pendingRequest->headers()->get('Authorization'))->toStartWith('SharedKey myaccount:');
});
