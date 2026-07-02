<?php

use CodebarAg\MicrosoftAzure\Data\OpenAi\ChatCompletionData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\EmbeddingData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\ModelListData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\OpenAiResponseData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ChatCompletions;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateFineTuningJob;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateImageGeneration;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateResponse;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateSpeech;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateTranscription;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1DeleteFile;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1Embeddings;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ListFiles;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ListModels;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1UploadFile;
use CodebarAg\MicrosoftAzure\Resources\OpenAiV1Resource;
use Saloon\Http\Faking\MockResponse;

it('resolves v1 endpoints without an api-version query', function (): void {
    $body = new GenericJsonPayload(['model' => 'gpt-4.1']);

    $requests = [
        [new V1ChatCompletions($body), '/openai/v1/chat/completions'],
        [new V1Embeddings($body), '/openai/v1/embeddings'],
        [new V1CreateResponse($body), '/openai/v1/responses'],
        [new V1ListModels, '/openai/v1/models'],
        [new V1ListFiles, '/openai/v1/files'],
        [new V1DeleteFile('file-1'), '/openai/v1/files/file-1'],
        [new V1CreateImageGeneration($body), '/openai/v1/images/generations'],
        [new V1CreateSpeech($body), '/openai/v1/audio/speech'],
        [new V1CreateTranscription($body), '/openai/v1/audio/transcriptions'],
        [new V1CreateFineTuningJob($body), '/openai/v1/fine_tuning/jobs'],
    ];

    foreach ($requests as [$request, $endpoint]) {
        expect($request->resolveEndpoint())->toBe($endpoint)
            ->and($request->query()->all())->toBe([]);
    }

    expect((new V1ChatCompletions($body))->body()->all())->toBe(['model' => 'gpt-4.1']);
});

it('rejects unreadable files on v1 uploads', function (): void {
    $request = new V1UploadFile('/nonexistent/file.jsonl', 'fine-tune');

    $request->body()->all();
})->throws(RuntimeException::class, 'is not readable');

it('exposes the v1 gateway from the openai resource', function (): void {
    $client = clientWithSeededToken();

    expect($client->openAi('my-openai')->v1())->toBeInstanceOf(OpenAiV1Resource::class);
});

it('runs chat, embeddings, responses and models through the v1 surface', function (): void {
    $client = clientWithOpenAiMock([
        V1ChatCompletions::class => MockResponse::make(body: [
            'id' => 'chatcmpl-1',
            'model' => 'gpt-4.1',
            'choices' => [['index' => 0, 'message' => ['role' => 'assistant', 'content' => 'Hello!']]],
        ]),
        V1Embeddings::class => MockResponse::make(body: [
            'data' => [['index' => 0, 'embedding' => [0.1, 0.2]]],
            'model' => 'text-embedding-3-large',
        ]),
        V1CreateResponse::class => MockResponse::make(body: [
            'id' => 'resp-1',
            'status' => 'completed',
            'output' => [['type' => 'message', 'content' => [['type' => 'output_text', 'text' => 'Hi']]]],
        ]),
        V1ListModels::class => MockResponse::make(body: [
            'data' => [['id' => 'gpt-4.1']],
        ]),
    ]);

    $v1 = $client->openAi('my-openai')->v1();

    expect($v1->chatCompletions(['model' => 'gpt-4.1', 'messages' => []]))->toBeInstanceOf(ChatCompletionData::class)
        ->and($v1->embeddings(['model' => 'text-embedding-3-large', 'input' => 'x']))->toBeInstanceOf(EmbeddingData::class)
        ->and($v1->responses(['model' => 'gpt-4.1', 'input' => 'Hi']))->toBeInstanceOf(OpenAiResponseData::class)
        ->and($v1->models())->toBeInstanceOf(ModelListData::class);
});

it('manages files, images, audio and fine tuning through the v1 surface', function (): void {
    $filePath = tempnam(sys_get_temp_dir(), 'lma-v1');
    file_put_contents((string) $filePath, '{"prompt": "x"}');

    $client = clientWithOpenAiMock([
        V1ListFiles::class => MockResponse::make(body: ['data' => [['id' => 'file-1']]]),
        V1UploadFile::class => MockResponse::make(body: ['id' => 'file-1', 'purpose' => 'fine-tune']),
        V1DeleteFile::class => MockResponse::make(body: ['id' => 'file-1', 'deleted' => true]),
        V1CreateImageGeneration::class => MockResponse::make(body: ['data' => [['url' => 'https://img']]]),
        V1CreateSpeech::class => MockResponse::make(body: ['ok' => true]),
        V1CreateTranscription::class => MockResponse::make(body: ['text' => 'hello']),
        V1CreateFineTuningJob::class => MockResponse::make(body: ['id' => 'ftjob-1']),
    ]);

    $v1 = $client->openAi('my-openai')->v1();

    expect($v1->listFiles()['data'])->toHaveCount(1)
        ->and($v1->uploadFile((string) $filePath, 'fine-tune')['id'])->toBe('file-1')
        ->and($v1->deleteFile('file-1')['deleted'])->toBeTrue()
        ->and($v1->imageGenerations(['model' => 'gpt-image-1', 'prompt' => 'a cat'])['data'])->toHaveCount(1)
        ->and($v1->speech(['model' => 'tts-1', 'input' => 'hi', 'voice' => 'alloy']))->toBe(['ok' => true])
        ->and($v1->transcriptions(['model' => 'whisper-1'])['text'])->toBe('hello')
        ->and($v1->createFineTuningJob(['model' => 'gpt-4.1', 'training_file' => 'file-1'])['id'])->toBe('ftjob-1');

    unlink((string) $filePath);
});
