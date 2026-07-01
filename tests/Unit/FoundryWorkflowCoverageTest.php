<?php

use CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\HostedAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\RaiConfigPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\UpdateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\WorkflowAgentDefinitionPayload;
use CodebarAg\MicrosoftAzure\Enums\AgentKind;
use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointInvocation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentVersions;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgent;
use CodebarAg\MicrosoftAzure\Requests\FunctionRuntime\RunDurableAgent;
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
