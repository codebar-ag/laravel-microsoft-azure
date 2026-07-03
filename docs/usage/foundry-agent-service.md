# Foundry Agent Service

Project-scoped agents, conversations, Responses API, MCP tool registration (toolboxes), connections, and skills. Legacy Assistants (threads/runs) are marked **deprecated** by Microsoft (sunset August 2026). Full REST path tables: [`ENDPOINTS.md` §7](../../ENDPOINTS.md#7-foundry-agent-service).

> **Permissions:** ARM `Contributor` on the subscription is enough to *create* the Cognitive Services account/project, but the agent data-plane (`agents()->create()` and friends) needs an additional role assigned at the account or project scope — Azure does **not** auto-grant this to the calling principal outside of Portal-driven creation. Grant a data-plane role such as **Azure AI User**/**Azure AI Developer** (naming varies by Azure AI Foundry release) explicitly, and expect it to take a few minutes to propagate before the first agent-write call succeeds.



```php
use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use CodebarAg\MicrosoftAzure\Facades\Azure;

$foundry = Azure::instance()->foundry('my-aif', 'my-prj');

$foundry->withFoundryFeatures([FoundryFeature::WorkflowAgents])
    ->agents()
    ->create(new CreateAgentPayload(
        name: 'doc-workflow',
        definition: new WorkflowAgentDefinitionPayload($csdlYaml),
    ));

$foundry->agents()->create(['name' => 'doc-agent', 'definition' => [...]]);
$foundry->agent('hosted-agent')->withFoundryFeatures([FoundryFeature::AgentEndpoints])
    ->endpoint()
    ->createResponse(['model' => 'gpt-5-mini', 'input' => 'Run step']);
$foundry->conversations()->create([]);
$foundry->responses()->create(['model' => 'gpt-5-mini', 'input' => 'Run step']);
```

`withFoundryFeatures([...])` sets the `Foundry-Features` request header needed for preview sub-surfaces (noted per-surface below); the core Agents/Conversations/Responses surface doesn't require it.

## Hosted-agent containers

Needs `Foundry-Features: HostedAgents=V1Preview`:

```php
$container = $foundry->withFoundryFeatures([FoundryFeature::HostedAgents])
    ->agent('hosted-agent')->version('3')->container();

$container->update(['image' => 'myregistry.azurecr.io/agent:v2']);
$container->awaitContainerOperation($operationId);
```

## Toolboxes (MCP registration)

Needs `Foundry-Features: Toolboxes=V1Preview` on every call:

```php
$toolboxes = $foundry->withFoundryFeatures([FoundryFeature::Toolboxes])->toolboxes();

$toolboxes->create(['name' => 'docuware-tools']);
$toolboxes->createVersion('docuware-tools', [
    'description' => 'DocuWare + Fetch tools',
    'tools' => [
        ['type' => 'mcp', 'server_label' => 'docuware', 'server_url' => 'https://docuware-mcp.azurewebsites.net/runtime/webhooks/mcp', 'require_approval' => 'never', 'project_connection_id' => 'docuware-mcp-conn'],
        ['type' => 'toolbox_search_preview'],
    ],
]);
$toolboxes->setDefaultVersion('docuware-tools', '1');
```

The MCP server referenced by `server_url` is registered, not provisioned — this package does not deploy MCP servers.

## Connections

Auth/secrets for MCP and other tools:

```php
$foundry->connections()->create(['name' => 'docuware-mcp-conn', 'kind' => 'remote-tool', 'auth_type' => 'custom-keys']);
```

Connections responses may contain credential material; success-path response bodies are **not** redacted by this package (see [Limitations](../limitations.md)) — be careful what you log.

## Skills

Versioned domain knowledge:

```php
$foundry->skills()->createVersion('doc-review', ['description' => 'Document review skill']);
```

## Smaller / preview sub-surfaces

These six are lower-priority/preview additions — full CRUD support, but expect rougher edges than the core surface above.

### Memory Stores

Knowledge/memory persistence. Uses `api-version=2025-11-15-preview`, distinct from the rest of Foundry (`v1`).

```php
$memoryStores = $foundry->memoryStores();
$memoryStores->create(['name' => 'support-memory']);
$memoryStores->update('ms-1', ['conversation_id' => 'conv-1']); // async — poll get() for completion
$memoryStores->search('ms-1', ['query' => 'last order status']);
```

### Evaluations

Standard CRUD, `api-version=v1`.

```php
$foundry->evaluations()->create(['name' => 'weekly-quality-check']);
$foundry->evaluations()->list();
```

### Schedules

Alternative/complementary trigger source to Azure Functions, with nested run history.

```php
$schedules = $foundry->schedules();
$schedules->createOrUpdate('daily-digest', ['cron' => '0 8 * * *']);
$schedules->runs('daily-digest')->list();
```

### Datasets

Version-scoped custom search/knowledge sources. **No list/discovery endpoint exists** — dataset names must already be known.

### Indexes

Structurally identical to Datasets (separate resource, same limitation — no list endpoint).

### Redteam

Safety/security test runs. **Create/list/get only** — no update or delete in the API (audit-log-style, assumed immutable by design).

## Not covered

- Agent Framework `WorkflowBuilder` graph authoring is .NET/Python SDK-only — this package exposes the HTTP APIs only.
- For the OpenAI-compatible, account-scoped variant of response generation (`POST /openai/v1/responses`), use `Azure::instance()->openAi($account)->v1()->responses([...])` (see [Foundry & Azure OpenAI](foundry-and-openai.md)) — a distinct surface from the project-scoped `foundry(...)->responses()` above.
- Legacy Threads/Runs (`/threads`) are deprecated (Microsoft sunset: August 2026) — new integrations should use Conversations/Responses instead.
