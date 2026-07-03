<?php

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Payload\AgentVersionRoutingPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CodeAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\ExternalAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\HostedAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RaiConfigPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\UpdateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\WorkflowAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Enums\AgentKind;
use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointInvocation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentContainerOperation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentContainerOperations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentVersions;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ReplaceAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\SetAgentVersionRouting;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgentContainer;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CompactConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\DeleteConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\DeleteConversationItem;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversationItem;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\ListConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\ListConversations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\UpdateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CancelResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\DeleteResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\GetResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\ListResponseInputItems;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunDurableAgent;
use CodebarAg\MicrosoftAzure\Resources\FoundryAgentContainerResource;
use Saloon\Http\Faking\MockResponse;

it('adds Foundry-Features header to preview agent requests', function (): void {
    $request = (new CreateAgent(new GenericJsonPayload(['name' => 'wf-1'])))
        ->withFoundryFeatures([FoundryFeature::WorkflowAgents]);

    expect($request->headers()->get('Foundry-Features'))
        ->toBe('WorkflowAgents=V1Preview');
});

it('applies scoped Foundry-Features from the resource gateway', function (): void {
    $client = clientWithFoundryMock([
        CreateAgent::class => function ($request) {
            expect($request->headers()->get('Foundry-Features'))->toBe('HostedAgents=V1Preview');

            return MockResponse::make(body: ['id' => 'agent-1', 'name' => 'hosted']);
        },
    ]);

    $client->foundry('my-foundry', 'default')
        ->withFoundryFeatures([FoundryFeature::HostedAgents])
        ->agents()
        ->create(['name' => 'hosted']);
});

it('filters agents by kind and supports agent lifecycle endpoints', function (): void {
    $client = clientWithFoundryMock([
        ListAgents::class => function ($request) {
            expect($request->query()->get('kind'))->toBe('workflow');

            return MockResponse::make(body: ['data' => [['name' => 'wf-1', 'kind' => 'workflow']]]);
        },
        UpdateAgent::class => MockResponse::make(body: ['id' => 'agent-1', 'name' => 'wf-1']),
        ListAgentVersions::class => MockResponse::make(body: ['data' => [['version' => '1'], ['version' => '2']]]),
        DeleteAgentVersion::class => MockResponse::make(status: 204),
    ]);

    $foundry = $client->foundry('my-foundry', 'default');

    expect($foundry->agents()->list(AgentKind::Workflow))->toHaveCount(1)
        ->and($foundry->agents()->update('wf-1', new UpdateAgentPayload(
            definition: new WorkflowAgentDefinitionPayload('kind: workflow'),
        )))->toHaveKey('name', 'wf-1')
        ->and($foundry->agents()->listVersions('wf-1'))->toHaveCount(2);

    $foundry->agents()->deleteVersion('wf-1', '1');
});

it('builds typed workflow and hosted agent payloads', function (): void {
    $workflow = new WorkflowAgentDefinitionPayload(
        workflow: 'kind: workflow',
        raiConfig: new RaiConfigPayload('default'),
    );

    $hosted = new HostedAgentDefinitionPayload(
        containerProtocolVersions: [['protocol' => 'responses', 'version' => '1.0']],
        cpu: '1',
        memory: '2Gi',
        image: 'myregistry.azurecr.io/agent:latest',
    );

    expect((new CreateAgentPayload('wf-1', $workflow))->toAzureBody())
        ->toMatchArray([
            'name' => 'wf-1',
            'definition' => [
                'kind' => 'workflow',
                'workflow' => 'kind: workflow',
                'rai_config' => ['rai_policy_name' => 'default'],
            ],
        ])
        ->and($hosted->toAzureBody())
        ->toMatchArray([
            'kind' => 'hosted',
            'cpu' => '1',
            'memory' => '2Gi',
            'image' => 'myregistry.azurecr.io/agent:latest',
        ]);
});

it('covers agent endpoint protocol and durable agent runtime gateways', function (): void {
    $foundryClient = clientWithFoundryMock([
        CreateAgentEndpointResponse::class => MockResponse::make(body: ['id' => 'resp-1', 'status' => 'completed']),
        CreateAgentEndpointInvocation::class => MockResponse::make(body: ['id' => 'inv-1', 'status' => 'completed']),
    ]);

    $agent = $foundryClient->foundry('my-foundry', 'default')->agent('hosted-agent');

    expect($agent->endpoint()->createResponse(['input' => 'hello']))->toHaveKey('id', 'resp-1')
        ->and($agent->endpoint()->createInvocation(['input' => 'hello']))->toHaveKey('id', 'inv-1');

    $runtimeClient = clientWithFunctionRuntimeMock([
        RunDurableAgent::class => MockResponse::make(body: ['id' => 'thread-1']),
    ]);

    expect($runtimeClient->functionRuntime('my-func')->agents()->run('MyDurableAgent', ['input' => 'test']))
        ->toHaveKey('id', 'thread-1');
});

it('adds Foundry-Features header to replace and canary-routing requests', function (): void {
    $replace = (new ReplaceAgent('agent-1', new GenericJsonPayload([])))
        ->withFoundryFeatures([FoundryFeature::WorkflowAgents]);

    expect($replace->headers()->get('Foundry-Features'))->toBe('WorkflowAgents=V1Preview');

    $routing = (new SetAgentVersionRouting('agent-1', new AgentVersionRoutingPayload([])))
        ->withFoundryFeatures([FoundryFeature::AgentEndpoints]);

    expect($routing->headers()->get('Foundry-Features'))->toBe('AgentEndpoints=V1Preview');
});

it('replaces an agent manifest and sets version routing via the resource gateway', function (): void {
    $client = clientWithFoundryMock([
        ReplaceAgent::class => MockResponse::make(body: ['id' => 'agent-1', 'name' => 'wf-1']),
        SetAgentVersionRouting::class => MockResponse::make(body: ['id' => 'agent-1', 'name' => 'wf-1']),
    ]);

    $agents = $client->foundry('my-foundry', 'default')->agents();

    expect($agents->replace('wf-1', ['definition' => ['kind' => 'workflow']]))->toHaveKey('name', 'wf-1')
        ->and($agents->setVersionRouting('wf-1', ['strategy' => 'canary']))->toHaveKey('name', 'wf-1');
});

it('builds typed code and external agent definition payloads with escape-hatch fields', function (): void {
    expect((new CodeAgentDefinitionPayload(['sandbox' => 'python3.12']))->toAzureBody())
        ->toBe(['kind' => 'code', 'sandbox' => 'python3.12'])
        ->and((new ExternalAgentDefinitionPayload(['endpoint' => 'https://example.com']))->toAzureBody())
        ->toBe(['kind' => 'external', 'endpoint' => 'https://example.com'])
        ->and((new AgentVersionRoutingPayload(['strategy' => 'canary']))->toAzureBody())
        ->toBe(['agent_endpoint' => ['version_selector' => ['strategy' => 'canary']]]);
});

it('applies HostedAgents feature and manages hosted-agent container operations', function (): void {
    $client = clientWithFoundryMock([
        UpdateAgentContainer::class => function ($request) {
            expect($request->headers()->get('Foundry-Features'))->toBe('HostedAgents=V1Preview');

            return MockResponse::make(body: ['status' => 'queued']);
        },
        ListAgentContainerOperations::class => MockResponse::make(body: ['data' => [['id' => 'op-1']]]),
        GetAgentContainerOperation::class => MockResponse::make(body: ['id' => 'op-1', 'status' => 'succeeded']),
    ]);

    $container = $client->foundry('my-foundry', 'default')
        ->withFoundryFeatures([FoundryFeature::HostedAgents])
        ->agent('hosted-agent')->version('3')->container();

    expect($container->update(['image' => 'registry/agent:v2']))->toHaveKey('status', 'queued')
        ->and($container->listOperations())->toHaveCount(1)
        ->and($container->getOperation('op-1'))->toHaveKey('status', 'succeeded');
});

function fakeClockContainerResource(AzureClient $client): FoundryAgentContainerResource
{
    return new class($client, 'my-foundry', 'default', null, [], 'hosted-agent', '3') extends FoundryAgentContainerResource
    {
        private int $fakeNow = 1_000_000;

        protected function now(): int
        {
            return $this->fakeNow;
        }

        protected function sleepSeconds(int $seconds): void
        {
            $this->fakeNow += $seconds;
        }
    };
}

it('polls a container operation until it reaches a terminal status', function (): void {
    $client = clientWithFoundryMock([
        MockResponse::make(body: ['id' => 'op-1', 'status' => 'running']),
        MockResponse::make(body: ['id' => 'op-1', 'status' => 'succeeded']),
    ]);

    $container = fakeClockContainerResource($client);

    $ticks = [];
    $result = $container->awaitContainerOperation('op-1', onTick: function (array $op) use (&$ticks): void {
        $ticks[] = $op['status'];
    });

    expect($result)->toHaveKey('status', 'succeeded')
        ->and($ticks)->toBe(['running', 'succeeded']);
});

it('throws when a container operation finishes in a failed state', function (): void {
    $client = clientWithFoundryMock([
        GetAgentContainerOperation::class => MockResponse::make(body: ['id' => 'op-1', 'status' => 'failed']),
    ]);

    $container = fakeClockContainerResource($client);

    expect(fn () => $container->awaitContainerOperation('op-1'))
        ->toThrow(LongRunningOperationException::class);
});

it('lists, updates, deletes and compacts conversations and items via the resource gateway', function (): void {
    $client = clientWithFoundryMock([
        ListConversations::class => MockResponse::make(body: ['data' => [['id' => 'conv-1']]]),
        UpdateConversation::class => MockResponse::make(body: ['id' => 'conv-1', 'title' => 'Updated']),
        DeleteConversation::class => MockResponse::make(status: 204),
        ListConversationItems::class => MockResponse::make(body: ['data' => [['id' => 'item-1']]]),
        GetConversationItem::class => MockResponse::make(body: ['id' => 'item-1']),
        DeleteConversationItem::class => MockResponse::make(status: 204),
        CompactConversation::class => MockResponse::make(body: ['id' => 'conv-1', 'compacted' => true]),
    ]);

    $conversations = $client->foundry('my-foundry', 'default')->conversations();

    expect($conversations->list())->toHaveCount(1)
        ->and($conversations->update('conv-1', ['title' => 'Updated']))->toHaveKey('title', 'Updated')
        ->and($conversations->listItems('conv-1'))->toHaveCount(1)
        ->and($conversations->getItem('conv-1', 'item-1'))->toHaveKey('id', 'item-1')
        ->and($conversations->compact('conv-1'))->toHaveKey('compacted', true);

    $conversations->delete('conv-1');
    $conversations->deleteItem('conv-1', 'item-1');
});

it('gets, cancels, deletes and lists input items of a response via the resource gateway', function (): void {
    $client = clientWithFoundryMock([
        GetResponse::class => MockResponse::make(body: ['id' => 'resp-1', 'status' => 'completed']),
        CancelResponse::class => MockResponse::make(body: ['id' => 'resp-1', 'status' => 'cancelled']),
        ListResponseInputItems::class => MockResponse::make(body: ['data' => [['id' => 'in-1']]]),
        DeleteResponse::class => MockResponse::make(status: 204),
    ]);

    $responses = $client->foundry('my-foundry', 'default')->responses();

    expect($responses->get('resp-1'))->toHaveKey('status', 'completed')
        ->and($responses->cancel('resp-1'))->toHaveKey('status', 'cancelled')
        ->and($responses->listInputItems('resp-1'))->toHaveCount(1);

    $responses->delete('resp-1');
});

it('resolves request paths for new workflow-related endpoints', function (): void {
    expect((new ListAgents(AgentKind::Workflow))->resolveEndpoint())->toBe('/agents')
        ->and((new UpdateAgent('wf-1', new GenericJsonPayload(['definition' => []])))->resolveEndpoint())
        ->toBe('/agents/wf-1')
        ->and((new ListAgentVersions('wf-1'))->resolveEndpoint())->toBe('/agents/wf-1/versions')
        ->and((new DeleteAgentVersion('wf-1', '2'))->resolveEndpoint())->toBe('/agents/wf-1/versions/2')
        ->and((new CreateAgentEndpointResponse('hosted', new GenericJsonPayload([])))->resolveEndpoint())
        ->toBe('/agents/hosted/endpoint/protocols/openai/responses')
        ->and((new CreateAgentEndpointInvocation('hosted', new GenericJsonPayload([])))->resolveEndpoint())
        ->toBe('/agents/hosted/endpoint/protocols/invocations')
        ->and((new RunDurableAgent('MyAgent', new GenericJsonPayload(['input' => true])))->resolveEndpoint())
        ->toBe('/api/agents/MyAgent/run');
});
