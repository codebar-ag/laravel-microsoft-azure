<?php

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;

it('serializes an empty body as a json object rather than an array', function (): void {
    $request = new CreateConversation(new GenericJsonPayload([]));

    expect((string) $request->body())->toBe('{}');
});

it('leaves a non-empty body unaffected', function (): void {
    $request = new CreateConversation(new GenericJsonPayload(['metadata' => ['a' => 'b']]));

    expect((string) $request->body())->toBe('{"metadata":{"a":"b"}}');
});
