<?php

use CodebarAg\MicrosoftAzure\Data\StorageQueue\QueueMessageData;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\DeleteMessage;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\ReceiveMessages;
use CodebarAg\MicrosoftAzure\Requests\StorageQueue\SendMessage;
use CodebarAg\MicrosoftAzure\Resources\StorageQueueResource;
use Saloon\Http\Faking\MockResponse;

function sentQueueMessageXmlFixture(): string
{
    return '<QueueMessagesList>'
        .'<QueueMessage>'
        .'<MessageId>msg-1</MessageId>'
        .'<InsertionTime>Wed, 01 Jan 2026 00:00:00 GMT</InsertionTime>'
        .'<ExpirationTime>Wed, 08 Jan 2026 00:00:00 GMT</ExpirationTime>'
        .'<PopReceipt>pop-1</PopReceipt>'
        .'<TimeNextVisible>Wed, 01 Jan 2026 00:00:30 GMT</TimeNextVisible>'
        .'</QueueMessage>'
        .'</QueueMessagesList>';
}

function receivedQueueMessagesXmlFixture(): string
{
    return '<QueueMessagesList>'
        .'<QueueMessage>'
        .'<MessageId>msg-1</MessageId>'
        .'<PopReceipt>pop-1</PopReceipt>'
        .'<DequeueCount>1</DequeueCount>'
        .'<MessageText>aGVsbG8gd29ybGQ=</MessageText>'
        .'</QueueMessage>'
        .'</QueueMessagesList>';
}

it('sends, receives and deletes queue messages via oauth', function (): void {
    $client = clientWithStorageQueueMock([
        SendMessage::class => MockResponse::make(body: sentQueueMessageXmlFixture()),
        ReceiveMessages::class => MockResponse::make(body: receivedQueueMessagesXmlFixture()),
        DeleteMessage::class => MockResponse::make(body: '', status: 204),
    ]);

    $queue = new StorageQueueResource($client, 'mystorageacct', 'myqueue');

    $sent = $queue->sendMessage('hello world', visibilityTimeoutSeconds: 5, messageTtlSeconds: 3600);
    $received = $queue->receiveMessages(numberOfMessages: 1);
    $queue->deleteMessage('msg-1', 'pop-1');

    expect($sent)->toBeInstanceOf(QueueMessageData::class)
        ->and($sent->messageId)->toBe('msg-1')
        ->and($sent->popReceipt)->toBe('pop-1')
        ->and($received)->toHaveCount(1)
        ->and($received->first())->toBeInstanceOf(QueueMessageData::class)
        ->and($received->first()?->dequeueCount)->toBe(1)
        ->and($received->first()?->messageText)->toBe('aGVsbG8gd29ybGQ=');
});

it('sends messages via shared key when an account key is provided', function (): void {
    $accountKey = 'TXlTdXBlclNlY3JldFN0b3JhZ2VBY2NvdW50S2V5MQ==';

    $client = clientWithStorageQueueMock(
        [SendMessage::class => MockResponse::make(body: sentQueueMessageXmlFixture())],
        accountKey: $accountKey,
    );

    $queue = new StorageQueueResource($client, 'mystorageacct', 'myqueue', $accountKey);

    $sent = $queue->sendMessage('hello world');

    expect($sent->messageId)->toBe('msg-1');
});
