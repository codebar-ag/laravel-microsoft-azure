<?php

use CodebarAg\MicrosoftAzure\Data\Payload\AgentVersionRoutingPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentContainerOperation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentContainerOperations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ReplaceAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\SetAgentVersionRouting;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgentContainer;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\CreateConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\DeleteConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\GetConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\ListConnections;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CompactConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\DeleteConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\DeleteConversationItem;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversationItem;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\ListConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\ListConversations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\UpdateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\CreateOrUpdateDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\DeleteDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\GetDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\CreateEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\DeleteEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\GetEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\ListEvaluations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\UpdateEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\CreateOrUpdateIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\DeleteIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\GetIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\CreateMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\DeleteMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\GetMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\ListMemoryStores;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\SearchMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\UpdateMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\CreateRedteam;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\GetRedteam;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Redteams\ListRedteams;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CancelResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\DeleteResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\GetResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\ListResponseInputItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\CreateOrUpdateSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\DeleteSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\GetSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\GetScheduleRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\ListScheduleRuns;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\ListSchedules;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\CreateSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\DeleteSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\GetSkill;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\GetSkillVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\ListSkills;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Skills\UpdateSkill;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadMessage;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\ListThreadMessages;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\SubmitThreadToolOutputs;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CallToolboxMcpTool;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CreateToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CreateToolboxVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\DeleteToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\GetToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxes;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxMcpTools;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxVersions;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\UpdateToolbox;
use Saloon\Http\Request;

dataset('foundry request endpoints', [
    'ListAgents' => [fn () => new ListAgents, '/agents', ApiVersion::FOUNDRY_AGENTS],
    'CreateAgent' => [fn () => new CreateAgent(new GenericJsonPayload(['name' => 'agent-1'])), '/agents', ApiVersion::FOUNDRY_AGENTS],
    'GetAgent' => [fn () => new GetAgent('agent-1'), '/agents/agent-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteAgent' => [fn () => new DeleteAgent('agent-1'), '/agents/agent-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateAgentVersion' => [fn () => new CreateAgentVersion('agent-1', new GenericJsonPayload([])), '/agents/agent-1/versions', ApiVersion::FOUNDRY_AGENTS],
    'GetAgentVersion' => [fn () => new GetAgentVersion('agent-1', '1'), '/agents/agent-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'ReplaceAgent' => [fn () => new ReplaceAgent('agent-1', new GenericJsonPayload([])), '/agents/agent-1', ApiVersion::FOUNDRY_AGENTS],
    'SetAgentVersionRouting' => [fn () => new SetAgentVersionRouting('agent-1', new AgentVersionRoutingPayload([])), '/agents/agent-1', ApiVersion::FOUNDRY_AGENTS],
    'UpdateAgentContainer' => [fn () => new UpdateAgentContainer('agent-1', '2', new GenericJsonPayload([])), '/agents/agent-1/versions/2/container', ApiVersion::FOUNDRY_AGENTS],
    'ListAgentContainerOperations' => [fn () => new ListAgentContainerOperations('agent-1', '2'), '/agents/agent-1/versions/2/containerOperations', ApiVersion::FOUNDRY_AGENTS],
    'GetAgentContainerOperation' => [fn () => new GetAgentContainerOperation('agent-1', '2', 'op-1'), '/agents/agent-1/versions/2/containerOperations/op-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateConversation' => [fn () => new CreateConversation(new GenericJsonPayload([])), '/conversations', ApiVersion::FOUNDRY_AGENTS],
    'ListConversations' => [fn () => new ListConversations, '/conversations', ApiVersion::FOUNDRY_AGENTS],
    'GetConversation' => [fn () => new GetConversation('conv-1'), '/conversations/conv-1', ApiVersion::FOUNDRY_AGENTS],
    'UpdateConversation' => [fn () => new UpdateConversation('conv-1', new GenericJsonPayload([])), '/conversations/conv-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteConversation' => [fn () => new DeleteConversation('conv-1'), '/conversations/conv-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateConversationItems' => [fn () => new CreateConversationItems('conv-1', new GenericJsonPayload([])), '/conversations/conv-1/items', ApiVersion::FOUNDRY_AGENTS],
    'ListConversationItems' => [fn () => new ListConversationItems('conv-1'), '/conversations/conv-1/items', ApiVersion::FOUNDRY_AGENTS],
    'GetConversationItem' => [fn () => new GetConversationItem('conv-1', 'item-1'), '/conversations/conv-1/items/item-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteConversationItem' => [fn () => new DeleteConversationItem('conv-1', 'item-1'), '/conversations/conv-1/items/item-1', ApiVersion::FOUNDRY_AGENTS],
    'CompactConversation' => [fn () => new CompactConversation('conv-1', new GenericJsonPayload([])), '/conversations/conv-1/compact', ApiVersion::FOUNDRY_AGENTS],
    'CreateProjectResponse' => [fn () => new CreateProjectResponse(new GenericJsonPayload([])), '/responses', ApiVersion::FOUNDRY_AGENTS],
    'GetResponse' => [fn () => new GetResponse('resp-1'), '/responses/resp-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteResponse' => [fn () => new DeleteResponse('resp-1'), '/responses/resp-1', ApiVersion::FOUNDRY_AGENTS],
    'CancelResponse' => [fn () => new CancelResponse('resp-1'), '/responses/resp-1/cancel', ApiVersion::FOUNDRY_AGENTS],
    'ListResponseInputItems' => [fn () => new ListResponseInputItems('resp-1'), '/responses/resp-1/input_items', ApiVersion::FOUNDRY_AGENTS],
    'ListToolboxes' => [fn () => new ListToolboxes, '/toolboxes', ApiVersion::FOUNDRY_AGENTS],
    'CreateToolbox' => [fn () => new CreateToolbox(new GenericJsonPayload(['name' => 'tb-1'])), '/toolboxes', ApiVersion::FOUNDRY_AGENTS],
    'GetToolbox' => [fn () => new GetToolbox('tb-1'), '/toolboxes/tb-1', ApiVersion::FOUNDRY_AGENTS],
    'UpdateToolbox' => [fn () => new UpdateToolbox('tb-1', new GenericJsonPayload(['default_version' => '2'])), '/toolboxes/tb-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteToolbox' => [fn () => new DeleteToolbox('tb-1'), '/toolboxes/tb-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateToolboxVersion' => [fn () => new CreateToolboxVersion('tb-1', new GenericJsonPayload([])), '/toolboxes/tb-1/versions', ApiVersion::FOUNDRY_AGENTS],
    'ListToolboxVersions' => [fn () => new ListToolboxVersions('tb-1'), '/toolboxes/tb-1/versions', ApiVersion::FOUNDRY_AGENTS],
    'ListToolboxMcpTools' => [fn () => new ListToolboxMcpTools('tb-1', '1'), '/toolboxes/tb-1/versions/1/mcp', ApiVersion::FOUNDRY_AGENTS],
    'CallToolboxMcpTool' => [fn () => new CallToolboxMcpTool('tb-1', '1', 'search', []), '/toolboxes/tb-1/versions/1/mcp', ApiVersion::FOUNDRY_AGENTS],
    'ListConnections' => [fn () => new ListConnections, '/connections', ApiVersion::FOUNDRY_AGENTS],
    'CreateConnection' => [fn () => new CreateConnection(new GenericJsonPayload(['name' => 'conn-1'])), '/connections', ApiVersion::FOUNDRY_AGENTS],
    'GetConnection' => [fn () => new GetConnection('conn-1'), '/connections/conn-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteConnection' => [fn () => new DeleteConnection('conn-1'), '/connections/conn-1', ApiVersion::FOUNDRY_AGENTS],
    'ListSkills' => [fn () => new ListSkills, '/skills', ApiVersion::FOUNDRY_AGENTS],
    'GetSkill' => [fn () => new GetSkill('skill-1'), '/skills/skill-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateSkillVersion' => [fn () => new CreateSkillVersion('skill-1', new GenericJsonPayload([])), '/skills/skill-1/versions', ApiVersion::FOUNDRY_AGENTS],
    'GetSkillVersion' => [fn () => new GetSkillVersion('skill-1', '1'), '/skills/skill-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteSkillVersion' => [fn () => new DeleteSkillVersion('skill-1', '1'), '/skills/skill-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'UpdateSkill' => [fn () => new UpdateSkill('skill-1', new GenericJsonPayload(['default_version' => '2'])), '/skills/skill-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateMemoryStore' => [fn () => new CreateMemoryStore(new GenericJsonPayload([])), '/memory_stores', ApiVersion::FOUNDRY_MEMORY_STORES],
    'ListMemoryStores' => [fn () => new ListMemoryStores, '/memory_stores', ApiVersion::FOUNDRY_MEMORY_STORES],
    'GetMemoryStore' => [fn () => new GetMemoryStore('ms-1'), '/memory_stores/ms-1', ApiVersion::FOUNDRY_MEMORY_STORES],
    'UpdateMemoryStore' => [fn () => new UpdateMemoryStore('ms-1', new GenericJsonPayload([])), '/memory_stores/ms-1', ApiVersion::FOUNDRY_MEMORY_STORES],
    'SearchMemoryStore' => [fn () => new SearchMemoryStore('ms-1', new GenericJsonPayload([])), '/memory_stores/ms-1/search', ApiVersion::FOUNDRY_MEMORY_STORES],
    'DeleteMemoryStore' => [fn () => new DeleteMemoryStore('ms-1'), '/memory_stores/ms-1', ApiVersion::FOUNDRY_MEMORY_STORES],
    'CreateEvaluation' => [fn () => new CreateEvaluation(new GenericJsonPayload([])), '/evaluations', ApiVersion::FOUNDRY_AGENTS],
    'ListEvaluations' => [fn () => new ListEvaluations, '/evaluations', ApiVersion::FOUNDRY_AGENTS],
    'GetEvaluation' => [fn () => new GetEvaluation('eval-1'), '/evaluations/eval-1', ApiVersion::FOUNDRY_AGENTS],
    'UpdateEvaluation' => [fn () => new UpdateEvaluation('eval-1', new GenericJsonPayload([])), '/evaluations/eval-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteEvaluation' => [fn () => new DeleteEvaluation('eval-1'), '/evaluations/eval-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateOrUpdateSchedule' => [fn () => new CreateOrUpdateSchedule('sched-1', new GenericJsonPayload([])), '/schedules/sched-1', ApiVersion::FOUNDRY_AGENTS],
    'ListSchedules' => [fn () => new ListSchedules, '/schedules', ApiVersion::FOUNDRY_AGENTS],
    'GetSchedule' => [fn () => new GetSchedule('sched-1'), '/schedules/sched-1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteSchedule' => [fn () => new DeleteSchedule('sched-1'), '/schedules/sched-1', ApiVersion::FOUNDRY_AGENTS],
    'ListScheduleRuns' => [fn () => new ListScheduleRuns('sched-1'), '/schedules/sched-1/runs', ApiVersion::FOUNDRY_AGENTS],
    'GetScheduleRun' => [fn () => new GetScheduleRun('sched-1', 'run-1'), '/schedules/sched-1/runs/run-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateOrUpdateDatasetVersion' => [fn () => new CreateOrUpdateDatasetVersion('ds-1', '1', new GenericJsonPayload([])), '/datasets/ds-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'GetDatasetVersion' => [fn () => new GetDatasetVersion('ds-1', '1'), '/datasets/ds-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteDatasetVersion' => [fn () => new DeleteDatasetVersion('ds-1', '1'), '/datasets/ds-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'CreateOrUpdateIndexVersion' => [fn () => new CreateOrUpdateIndexVersion('idx-1', '1', new GenericJsonPayload([])), '/indexes/idx-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'GetIndexVersion' => [fn () => new GetIndexVersion('idx-1', '1'), '/indexes/idx-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'DeleteIndexVersion' => [fn () => new DeleteIndexVersion('idx-1', '1'), '/indexes/idx-1/versions/1', ApiVersion::FOUNDRY_AGENTS],
    'CreateRedteam' => [fn () => new CreateRedteam(new GenericJsonPayload([])), '/redteams', ApiVersion::FOUNDRY_AGENTS],
    'ListRedteams' => [fn () => new ListRedteams, '/redteams', ApiVersion::FOUNDRY_AGENTS],
    'GetRedteam' => [fn () => new GetRedteam('rt-1'), '/redteams/rt-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateThread' => [fn () => new CreateThread(new GenericJsonPayload([])), '/threads', ApiVersion::FOUNDRY_AGENTS],
    'GetThread' => [fn () => new GetThread('thread-1'), '/threads/thread-1', ApiVersion::FOUNDRY_AGENTS],
    'CreateThreadMessage' => [fn () => new CreateThreadMessage('thread-1', new GenericJsonPayload([])), '/threads/thread-1/messages', ApiVersion::FOUNDRY_AGENTS],
    'ListThreadMessages' => [fn () => new ListThreadMessages('thread-1'), '/threads/thread-1/messages', ApiVersion::FOUNDRY_AGENTS],
    'CreateThreadRun' => [fn () => new CreateThreadRun('thread-1', new GenericJsonPayload([])), '/threads/thread-1/runs', ApiVersion::FOUNDRY_AGENTS],
    'GetThreadRun' => [fn () => new GetThreadRun('thread-1', 'run-1'), '/threads/thread-1/runs/run-1', ApiVersion::FOUNDRY_AGENTS],
    'SubmitThreadToolOutputs' => [fn () => new SubmitThreadToolOutputs('thread-1', 'run-1', new GenericJsonPayload([])), '/threads/thread-1/runs/run-1/submit_tool_outputs', ApiVersion::FOUNDRY_AGENTS],
]);

it('resolves foundry request endpoints and api-version query', function (callable $factory, string $endpoint, ApiVersion $apiVersion): void {
    /** @var Request $request */
    $request = $factory();

    expect($request->resolveEndpoint())->toBe($endpoint)
        ->and($request->query()->all())->toBe(['api-version' => $apiVersion->value()]);
})->with('foundry request endpoints');
