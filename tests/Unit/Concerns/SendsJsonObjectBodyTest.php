<?php

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;
use Saloon\Enums\Method;
use Saloon\Http\Request;

it('serializes an empty body as a json object rather than an array', function (): void {
    $request = new CreateConversation(new GenericJsonPayload([]));

    expect((string) $request->body())->toBe('{}');
});

it('leaves a non-empty body unaffected', function (): void {
    $request = new CreateConversation(new GenericJsonPayload(['metadata' => ['a' => 'b']]));

    expect((string) $request->body())->toBe('{"metadata":{"a":"b"}}');
});

it('defaults to an empty body when a request does not override defaultBody', function (): void {
    $request = new class extends Request
    {
        use SendsJsonObjectBody;

        protected Method $method = Method::POST;

        public function resolveEndpoint(): string
        {
            return '/test';
        }
    };

    expect($request->body()->all())->toBe([]);
});
