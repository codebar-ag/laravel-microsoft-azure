<?php

use CodebarAg\MicrosoftAzure\Data\Arm\ProjectConnectionData;
use CodebarAg\MicrosoftAzure\Data\Payload\ProjectConnectionPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\CreateOrUpdateProjectConnection;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\DeleteProjectConnection;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\GetProjectConnection;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections\ListProjectConnections;
use CodebarAg\MicrosoftAzure\Resources\FoundryProjectsResource;
use CodebarAg\MicrosoftAzure\Resources\ProjectConnectionsResource;
use Saloon\Http\Faking\MockResponse;

function projectConnectionFixture(): array
{
    return [
        'id' => '/subscriptions/sub-1/resourceGroups/rg-test/providers/Microsoft.CognitiveServices/accounts/acct-1/projects/proj-1/connections/mcp-abc123',
        'name' => 'mcp-abc123',
        'properties' => [
            'category' => 'GenericHttp',
            'authType' => 'CustomKeys',
            'target' => 'https://mcp.example.com/mcp',
        ],
    ];
}

function projectConnections(array $responses): ProjectConnectionsResource
{
    return new ProjectConnectionsResource(clientWithArmMock($responses), 'sub-1', 'rg-test', 'acct-1', 'proj-1');
}

it('lists project connections', function (): void {
    $connections = projectConnections([
        ListProjectConnections::class => MockResponse::make(body: ['value' => [projectConnectionFixture()]]),
    ])->list();

    expect($connections)->toHaveCount(1)
        ->and($connections->first())->toBeInstanceOf(ProjectConnectionData::class)
        ->and($connections->first()?->name)->toBe('mcp-abc123')
        ->and($connections->first()?->category)->toBe('GenericHttp')
        ->and($connections->first()?->authType)->toBe('CustomKeys')
        ->and($connections->first()?->target)->toBe('https://mcp.example.com/mcp');
});

it('gets a project connection', function (): void {
    $connection = projectConnections([
        GetProjectConnection::class => MockResponse::make(body: projectConnectionFixture()),
    ])->get('mcp-abc123');

    expect($connection->name)->toBe('mcp-abc123')
        ->and($connection->target)->toBe('https://mcp.example.com/mcp');
});

it('creates or updates a project connection with credentials', function (): void {
    $connection = projectConnections([
        CreateOrUpdateProjectConnection::class => MockResponse::make(body: projectConnectionFixture()),
    ])->createOrUpdate('mcp-abc123', new ProjectConnectionPayload(
        category: 'GenericHttp',
        authType: 'CustomKeys',
        target: 'https://mcp.example.com/mcp',
        credentials: ['keys' => ['apiKey' => 'secret-token']],
    ));

    expect($connection)->toBeInstanceOf(ProjectConnectionData::class)
        ->and($connection->name)->toBe('mcp-abc123');
});

it('creates a project connection without credentials', function (): void {
    $connection = projectConnections([
        CreateOrUpdateProjectConnection::class => MockResponse::make(body: [
            'id' => 'x',
            'name' => 'mcp-noauth',
            'properties' => ['category' => 'GenericHttp', 'authType' => 'None', 'target' => 'https://mcp.example.com/mcp'],
        ]),
    ])->createOrUpdate('mcp-noauth', new ProjectConnectionPayload(
        category: 'GenericHttp',
        authType: 'None',
        target: 'https://mcp.example.com/mcp',
    ));

    expect($connection->authType)->toBe('None');
});

it('deletes a project connection', function (): void {
    $connections = projectConnections([
        DeleteProjectConnection::class => MockResponse::make(body: '', status: 200),
    ]);

    $connections->delete('mcp-abc123');

    expect(true)->toBeTrue();
});

it('tolerates a connection payload with no properties', function (): void {
    $connection = projectConnections([
        GetProjectConnection::class => MockResponse::make(body: ['id' => 'x', 'name' => 'mcp-bare']),
    ])->get('mcp-bare');

    expect($connection->category)->toBeNull()
        ->and($connection->authType)->toBeNull()
        ->and($connection->target)->toBeNull();
});

it('exposes project connections from a foundry project via the account gateway', function (): void {
    $client = clientWithArmMock([
        ListProjectConnections::class => MockResponse::make(body: ['value' => []]),
    ]);

    $projects = new FoundryProjectsResource($client, 'sub-1', 'rg-test', 'acct-1');

    expect($projects->connections('proj-1'))
        ->toBeInstanceOf(ProjectConnectionsResource::class)
        ->and($projects->connections('proj-1')->list())->toHaveCount(0);
});
