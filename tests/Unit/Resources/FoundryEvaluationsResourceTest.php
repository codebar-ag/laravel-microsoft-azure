<?php

use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\CreateEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\DeleteEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\GetEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\ListEvaluations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\UpdateEvaluation;
use CodebarAg\MicrosoftAzure\Resources\FoundryEvaluationsResource;
use Saloon\Http\Faking\MockResponse;

it('creates, lists, gets, updates and deletes evaluations', function (): void {
    $client = clientWithFoundryMock([
        CreateEvaluation::class => MockResponse::make(body: ['id' => 'eval-1']),
        ListEvaluations::class => MockResponse::make(body: ['data' => [['id' => 'eval-1']]]),
        GetEvaluation::class => MockResponse::make(body: ['id' => 'eval-1', 'status' => 'completed']),
        UpdateEvaluation::class => MockResponse::make(body: ['id' => 'eval-1', 'status' => 'completed']),
        DeleteEvaluation::class => MockResponse::make(status: 204),
    ]);

    $evaluations = $client->foundry('my-foundry', 'default')->evaluations();

    expect($evaluations)->toBeInstanceOf(FoundryEvaluationsResource::class)
        ->and($evaluations->create([]))->toHaveKey('id')
        ->and($evaluations->list())->toHaveCount(1)
        ->and($evaluations->get('eval-1'))->toHaveKey('status')
        ->and($evaluations->update('eval-1', ['status' => 'completed']))->toHaveKey('status');

    $evaluations->delete('eval-1');
});
