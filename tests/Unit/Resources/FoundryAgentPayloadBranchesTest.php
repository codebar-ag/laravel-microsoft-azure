<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\UpdateAgentPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgent;
use Saloon\Http\Faking\MockResponse;

it('accepts typed payloads for agent create, update and version creation', function (): void {
    $client = clientWithFoundryMock([
        CreateAgent::class => MockResponse::make(body: ['id' => 'agent-1', 'name' => 'my-agent']),
        UpdateAgent::class => MockResponse::make(body: ['id' => 'agent-1', 'description' => 'Updated']),
        CreateAgentVersion::class => MockResponse::make(body: ['id' => 'agent-1', 'version' => '2']),
    ]);

    $agents = $client->foundry('my-foundry', 'default')->agents();

    $created = $agents->create(new CreateAgentPayload('my-agent', new GenericJsonPayload(['kind' => 'prompt'])));
    $updated = $agents->update('my-agent', new UpdateAgentPayload(new GenericJsonPayload(['kind' => 'prompt']), 'Updated'));
    $updatedFromArray = $agents->update('my-agent', ['description' => 'Updated']);
    $version = $agents->createVersion('my-agent', new UpdateAgentPayload(new GenericJsonPayload(['kind' => 'prompt'])));

    expect($created['name'])->toBe('my-agent')
        ->and($updated['description'])->toBe('Updated')
        ->and($updatedFromArray['description'])->toBe('Updated')
        ->and($version['version'])->toBe('2');
});
