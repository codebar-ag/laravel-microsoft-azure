<?php

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadMessage;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\ListThreadMessages;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\SubmitThreadToolOutputs;
use Saloon\Http\Request;

dataset('foundry request endpoints', [
    'ListAgents' => [fn () => new ListAgents, '/agents', ApiVersion::FOUNDRY_AGENTS],
    'CreateAgent' => [fn () => new CreateAgent(new GenericJsonPayload(['name' => 'agent-1'])), '/agents', ApiVersion::FOUNDRY_AGENTS],
    'GetAgent' => [fn () => new GetAgent('agent-1'), '/agents/agent-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteAgent' => [fn () => new DeleteAgent('agent-1'), '/agents/agent-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateAgentVersion' => [fn () => new CreateAgentVersion('agent-1', new GenericJsonPayload([])), '/agents/agent-1/versions', ApiVersion::FOUNDRY_AGENTS],
    'GetAgentVersion' => [fn () => new GetAgentVersion('agent-1', '1'), '/agents/agent-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'CreateConversation' => [fn () => new CreateConversation(new GenericJsonPayload([])), '/conversations', ApiVersion::FOUNDRY_AGENTS],
    'GetConversation' => [fn () => new GetConversation('conv-1'), '/conversations/conv-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateConversationItems' => [fn () => new CreateConversationItems('conv-1', new GenericJsonPayload([])), '/conversations/conv-1/items', ApiVersion::FOUNDRY_AGENTS],
    'CreateProjectResponse' => [fn () => new CreateProjectResponse(new GenericJsonPayload([])), '/responses', ApiVersion::FOUNDRY_AGENTS],
    'CreateThread' => [fn () => new CreateThread(new GenericJsonPayload([])), '/threads', ApiVersion::FOUNDRY_AGENTS],
    'GetThread' => [fn () => new GetThread('thread-1'), '/threads/thread-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateThreadMessage' => [fn () => new CreateThreadMessage('thread-1', new GenericJsonPayload([])), '/threads/thread-1/messages', ApiVersion::FOUNDRY_AGENTS],
    'ListThreadMessages' => [fn () => new ListThreadMessages('thread-1'), '/threads/thread-1/messages', ApiVersion::FOUNDRY_AGENTS],
    'CreateThreadRun' => [fn () => new CreateThreadRun('thread-1', new GenericJsonPayload([])), '/threads/thread-1/runs', ApiVersion::FOUNDRY_AGENTS],
    'GetThreadRun' => [fn () => new GetThreadRun('thread-1', 'run-1'), '/threads/thread-1/runs/run-1', ApiVersion::FOUNDRY_AGENTS],
    'SubmitThreadToolOutputs' => [fn () => new SubmitThreadToolOutputs('thread-1', 'run-1', new GenericJsonPayload([])), '/threads/thread-1/runs/run-1/submit_tool_outputs', ApiVersion::FOUNDRY_AGENTS],
]);

it('resolves foundry request endpoints and api-version query', function (callable $factory, string $endpoint, string $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion]);
})->with('foundry request endpoints');
