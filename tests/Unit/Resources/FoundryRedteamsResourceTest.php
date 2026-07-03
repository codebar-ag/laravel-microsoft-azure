<?php

use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\CreateRedteam;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\GetRedteam;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\ListRedteams;
use CodebarAg\MicrosoftAzure\Resources\FoundryRedteamsResource;
use Saloon\Http\Faking\MockResponse;

it('creates, lists and gets redteam runs', function (): void {
    $client = clientWithFoundryMock([
        CreateRedteam::class => MockResponse::make(body: ['name' => 'rt-1']),
        ListRedteams::class => MockResponse::make(body: ['data' => [['name' => 'rt-1']]]),
        GetRedteam::class => MockResponse::make(body: ['name' => 'rt-1', 'status' => 'completed']),
    ]);

    $redteams = $client->foundry('my-foundry', 'default')->redteams();

    expect($redteams)->toBeInstanceOf(FoundryRedteamsResource::class)
        ->and($redteams->create(['target_agent' => 'doc-agent']))->toHaveKey('name', 'rt-1')
        ->and($redteams->list())->toHaveCount(1)
        ->and($redteams->get('rt-1'))->toHaveKey('status', 'completed');
});
