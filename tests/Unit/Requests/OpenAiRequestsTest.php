<?php

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ChatCompletions;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateFineTuningJob;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateImageGeneration;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateResponses;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateSpeech;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateTranscription;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\DeleteFile;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\Embeddings;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ListFiles;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ListModels;
use Saloon\Http\Request;

dataset('openai request endpoints', [
    'ChatCompletions' => [
        fn () => new ChatCompletions('gpt-4o', new GenericJsonPayload(['messages' => []])),
        '/openai/deployments/gpt-4o/chat/completions',
        ApiVersion::OPENAI,
    ],
    'Embeddings' => [
        fn () => new Embeddings('embed', new GenericJsonPayload(['input' => 'hello'])),
        '/openai/deployments/embed/embeddings',
        ApiVersion::OPENAI,
    ],
    'ListModels' => [
        fn () => new ListModels,
        '/openai/models',
        ApiVersion::OPENAI,
    ],
    'CreateResponses' => [
        fn () => new CreateResponses(new GenericJsonPayload(['input' => 'hello'])),
        '/openai/responses',
        ApiVersion::OPENAI_PREVIEW,
    ],
    'CreateSpeech' => [
        fn () => new CreateSpeech('tts', new GenericJsonPayload(['input' => 'hello'])),
        '/openai/deployments/tts/audio/speech',
        ApiVersion::OPENAI,
    ],
    'CreateTranscription' => [
        fn () => new CreateTranscription('whisper', new GenericJsonPayload([])),
        '/openai/deployments/whisper/audio/transcriptions',
        ApiVersion::OPENAI,
    ],
    'CreateImageGeneration' => [
        fn () => new CreateImageGeneration('dalle', new GenericJsonPayload(['prompt' => 'cat'])),
        '/openai/deployments/dalle/images/generations',
        ApiVersion::OPENAI,
    ],
    'ListFiles' => [
        fn () => new ListFiles,
        '/openai/files',
        ApiVersion::OPENAI,
    ],
    'DeleteFile' => [
        fn () => new DeleteFile('file-1'),
        '/openai/files/file-1',
        ApiVersion::OPENAI,
    ],
    'CreateFineTuningJob' => [
        fn () => new CreateFineTuningJob(new GenericJsonPayload(['model' => 'gpt-4o'])),
        '/openai/fine_tuning/jobs',
        ApiVersion::OPENAI,
    ],
]);

it('resolves openai request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('openai request endpoints');

it('passes generic json payload through chat completions body', function (): void {
    $request = new ChatCompletions('gpt-4o', new GenericJsonPayload([
        'messages' => [['role' => 'user', 'content' => 'Hi']],
    ]));

    expect($request->body()->all())
        ->toBe(['messages' => [['role' => 'user', 'content' => 'Hi']]]);
});
