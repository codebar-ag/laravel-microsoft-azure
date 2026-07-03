<?php

use CodebarAg\MicrosoftAzure\Requests\StorageQueue\DeleteMessage;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\ReceiveMessages;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\SendMessage;

it('resolves send message endpoint, query and xml body', function (): void {
    $request = new SendMessage('myqueue', base64_encode('hello world'), visibilityTimeoutSeconds: 5, messageTtlSeconds: 3600);

    expect($request->resolveEndpoint())->toBe('/myqueue/messages')
        ->and($request->query()->all())->toBe(['visibilitytimeout' => 5, 'messagettl' => 3600])
        ->and($request->body()->all())->toBe('<QueueMessage><MessageText>'.base64_encode('hello world').'</MessageText></QueueMessage>')
        ->and($request->headers()->all())->toHaveKey('Content-Type', 'application/xml');
});

it('omits null query params on send message', function (): void {
    $request = new SendMessage('myqueue', base64_encode('hello'));

    expect($request->query()->all())->toBe([]);
});

it('resolves receive messages endpoint and query', function (): void {
    $request = new ReceiveMessages('myqueue', numberOfMessages: 10, visibilityTimeoutSeconds: 30);

    expect($request->resolveEndpoint())->toBe('/myqueue/messages')
        ->and($request->query()->all())->toBe(['numofmessages' => 10, 'visibilitytimeout' => 30]);
});

it('defaults receive messages to a single message and no explicit visibility timeout', function (): void {
    $request = new ReceiveMessages('myqueue');

    expect($request->query()->all())->toBe(['numofmessages' => 1]);
});

it('resolves delete message endpoint and popreceipt query', function (): void {
    $request = new DeleteMessage('myqueue', 'msg-1', 'pop-receipt-value');

    expect($request->resolveEndpoint())->toBe('/myqueue/messages/msg-1')
        ->and($request->query()->all())->toBe(['popreceipt' => 'pop-receipt-value']);
});
