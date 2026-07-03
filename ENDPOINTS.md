# Endpoint catalog

Human-readable index of every REST surface in `laravel-microsoft-azure`. For class-level mappings see [docs/api-reference.md](docs/api-reference.md) (auto-generated). For parity status see [docs/inventory-parity.md](docs/inventory-parity.md).

**238 Saloon request classes** across **13 surfaces**. All wrappers are REST-only — no .NET SDK or Agent Framework runtime code in this package.

## Authentication scopes

| Surface | Base URL | Auth |
| --- | --- | --- |
| ARM / Functions ARM / Foundry control plane / Logic Apps / API Management | `https://management.azure.com` | Entra client credentials — `https://management.azure.com/.default` |
| Key Vault | `https://{vault}.vault.azure.net` | Entra — `https://vault.azure.net/.default` |
| Storage Queue data plane | `https://{account}.queue.core.windows.net` | Entra — `https://storage.azure.com/.default` **or** Shared Key (HMAC-SHA256 over the account key) |
| Microsoft Graph | `https://graph.microsoft.com/v1.0` | Entra — `https://graph.microsoft.com/.default` |
| Kudu (SCM) | `https://{app}.scm.azurewebsites.net` | Entra — scoped to SCM host |
| Azure OpenAI data plane (dated + v1) | `https://{account}.openai.azure.com` | Entra — `https://cognitiveservices.azure.com/.default` **or** `api-key` header |
| Foundry Agent Service | `https://{account}.services.ai.azure.com/api/projects/{project}` | Same as OpenAI data plane |
| Function runtime | `https://{app}.azurewebsites.net` | `x-functions-key` / host key **or** Entra scoped to app host |
| Log Analytics query | `https://api.loganalytics.azure.com/v1` | Entra — `https://api.loganalytics.io/.default` |
| OAuth (internal) | `https://login.microsoftonline.com` | Client credentials |

---

## 1. ARM — core infrastructure

Subscriptions, resource groups, template deployments, RBAC, SQL, deleted-resource purge.

| Gateway | Example |
| --- | --- |
| `subscriptions()` | List/get/cancel subscriptions |
| `subscriptionAliases()` | Billing-scope alias CRUD |
| `resourceGroups($sub)` | RG CRUD + list |
| `deployments($sub, $rg)` | ARM template deploy, poll operations, cancel |
| `roleAssignments($scope)` | Create role assignment |
| `deletedVaults($sub)` | List/purge soft-deleted Key Vaults |
| `deletedCognitiveServices($sub)` | List/purge soft-deleted AI accounts |
| `sql($sub, $rg, $server)` | SQL firewall rules |
| `sqlDatabases($sub, $rg, $server)` | Get database |

Microsoft docs: [Azure Resource Manager REST](https://learn.microsoft.com/en-us/rest/api/resources/)

---

## 2. Foundry control plane (ARM)

Manage AI Services accounts, Foundry projects, and model deployments. API version `2026-05-01`.

```php
$cs = Azure::instance()->cognitiveServices($sub, $rg);

$cs->account('my-aif')->createOrUpdate(
    location: 'westeurope',
    properties: [
        'customSubDomainName' => 'my-aif',
        'disableLocalAuth' => false,
        'allowProjectManagement' => true,
    ],
    identityType: 'SystemAssigned', // required by Azure to create projects on this account
);

$keys = $cs->account('my-aif')->listKeys();
$cs->account('my-aif')->regenerateKey('Key1');

$cs->account('my-aif')->deployments()->createOrUpdate(
    deploymentName: 'gpt-5-mini',
    modelFormat: 'OpenAI',
    modelName: 'gpt-5-mini',
    modelVersion: '2025-08-07',
    skuName: 'GlobalStandard',
    skuCapacity: 10,
);

$cs->account('my-aif')->projects()->createOrUpdate('my-prj', 'westeurope');
```

| Operation | REST path (relative to ARM) |
| --- | --- |
| List accounts (subscription) | `GET .../providers/Microsoft.CognitiveServices/accounts` |
| List accounts (RG) | `GET .../resourceGroups/{rg}/providers/Microsoft.CognitiveServices/accounts` |
| Account CRUD | `PUT/GET/PATCH/DELETE .../accounts/{account}` |
| List / regenerate keys | `POST .../accounts/{account}/listKeys`, `.../regenerateKey` |
| List models / SKUs | `GET .../accounts/{account}/models`, `.../skus` |
| Project CRUD | `PUT/GET/DELETE .../accounts/{account}/projects/{project}` |
| Deployment CRUD | `PUT/GET/DELETE .../accounts/{account}/deployments/{deployment}` |

Microsoft docs: [AI Foundry account management REST](https://learn.microsoft.com/en-us/rest/api/aifoundry/accountmanagement/)

---

## 3. Azure Functions ARM (`Microsoft.Web`)

Function App lifecycle, app settings, host/function keys, trigger sync. API version `2024-11-01`.

```php
$app = Azure::instance()->functionApps($sub, $rg)->app('my-func');

$app->get();
$app->restart();
$app->settings()->update(['FUNCTIONS_WORKER_RUNTIME' => 'dotnet-isolated']);
$app->hostKeys()->list();
$app->functions('FlowRunner')->keys()->list();
$app->syncTriggers();
```

| Operation | REST path |
| --- | --- |
| Site CRUD / list | `PUT/GET/DELETE .../Microsoft.Web/sites/{name}` |
| Restart / start / stop | `POST .../sites/{name}/restart` etc. |
| Site config | `GET/PUT .../sites/{name}/config/web` |
| App settings | `POST .../config/appsettings/list`, `PUT .../config/appsettings` |
| Connection strings | `POST .../config/connectionstrings/list` |
| Functions list/get | `GET .../sites/{name}/functions` |
| Host keys | `POST .../host/default/listkeys`, `PUT/DELETE .../host/default/keys/{key}` |
| Function keys | `POST .../functions/{fn}/listkeys`, `PUT/DELETE .../functions/{fn}/keys/{key}` |
| Sync triggers | `POST .../syncfunctiontriggers`, `.../syncfunctiontriggers/status` |

Microsoft docs: [Web Apps REST](https://learn.microsoft.com/en-us/rest/api/appservice/web-apps)

Kudu zip deploy remains on `appService($name)->zipDeploy()`.

---

## 4. API Management subscriptions (`Microsoft.ApiManagement`)

Multi-tenant API-key-style credential management for an existing APIM instance (product/API/policy configuration is out of scope). API version `2022-08-01`.

```php
$subs = Azure::instance()->apiManagement($sub, $rg)->service('my-apim')->subscriptions();

$subscription = $subs->create('partner-a', 'Partner A', scope: '/products/partner-tier');
$keys = $subs->subscription('partner-a')->listSecrets(); // primaryKey/secondaryKey only available here
$subs->subscription('partner-a')->regeneratePrimaryKey();
$subs->subscription('partner-a')->revoke(); // PATCH state=suspended, If-Match: *
```

| Operation | REST path |
| --- | --- |
| Subscription CRUD / list | `PUT/GET .../service/{name}/subscriptions/{sid}`, `GET .../subscriptions` |
| Key rotation | `POST .../subscriptions/{sid}/regeneratePrimaryKey`, `.../regenerateSecondaryKey` |
| Key retrieval | `POST .../subscriptions/{sid}/listSecrets` (keys are never returned by GET/PUT) |
| Revoke | `PATCH .../subscriptions/{sid}` (`If-Match: *`, `properties.state=suspended`) |

**Skipped:** products, policies, APIs, named values, users — subscription CRUD + key lifecycle only.

Microsoft docs: [API Management REST — Subscription](https://learn.microsoft.com/en-us/rest/api/apimanagement/subscription)

---

## 5. Logic Apps (`Microsoft.Logic`)

Workflow definitions, run-time triggers, run history, and per-run actions. API version `2019-05-01`. 30 request classes, all ARM-scoped (`management.azure.com`).

```php
$workflows = Azure::instance()->logicWorkflows($sub, $rg);

$workflows->createOrUpdate(
    workflowName: 'invoice-router',
    location: 'westeurope',
    definition: $workflowDefinitionJson,
    state: 'Enabled',
);

$workflow = $workflows->workflow('invoice-router');
$workflow->enable();
$workflow->triggers()->trigger('manual')->run();
$workflow->runs()->list();
$workflow->runs()->run($runId)->actions()->list();
```

| Group | Operations | Tier |
| --- | --- | --- |
| Workflows | CRUD, list (subscription/RG), enable/disable, listCallbackUrl | required |
| Workflows | generateUpgradedDefinition, regenerateAccessKey, validate | extended |
| Versions | list, get | extended |
| Triggers | list, get, run, listCallbackUrl | required |
| Triggers | reset, schemas/json, setState | extended |
| Trigger histories | list, get | required |
| Trigger histories | resubmit | extended |
| Runs | list, get, cancel | required |
| Run actions | list, get | required |
| Run actions | listExpressionTraces | extended |

**Skipped:** Workflows `move` (ISE-only — Integration Service Environments are retired), `listSwagger` (designer artifact, not needed for headless callers), workflow-version triggers `listCallbackUrl` (redundant with the live-trigger callback URL), and run-action `repetitions` / `requestHistories` (high class count for low value — the parent action payload already carries status and error detail).

Microsoft docs: [Logic Apps REST](https://learn.microsoft.com/en-us/rest/api/logic/)

---

## 6. Azure OpenAI data plane

Inference and file APIs against `{account}.openai.azure.com`. Supports Entra or API key auth. Two surfaces: the dated api-version surface (deployment in the path) and the newer v1 surface (model in the body).

```php
$openai = Azure::instance()->openAi('my-aif'); // optional 2nd arg: api key

$openai->chat()->create('gpt-5-mini', [
    'messages' => [['role' => 'user', 'content' => 'Hello']],
]);

$openai->embeddings()->create('embed-model', ['input' => 'text']);
$openai->models()->list();
$openai->responses()->create(['model' => 'gpt-5-mini', 'input' => 'Hello']);
```

| Operation | Path | Tier |
| --- | --- | --- |
| Chat completions | `POST /openai/deployments/{id}/chat/completions` | required |
| Embeddings | `POST /openai/deployments/{id}/embeddings` | required |
| List models | `GET /openai/models` | required |
| Responses API | `POST /openai/responses` | required |
| Speech / transcription | `POST .../audio/speech`, `.../transcriptions` | extended |
| Image generation | `POST .../images/generations` | extended |
| Files | `GET/POST/DELETE /openai/files` | extended |
| Fine-tuning jobs | `POST /openai/fine_tuning/jobs` | extended |

Microsoft docs: [Azure OpenAI REST](https://learn.microsoft.com/en-us/azure/ai-foundry/openai/reference)

### OpenAI v1 (GA, unversioned)

GA since August 2025. Paths live under unversioned `/openai/v1/*` — no `api-version` query
parameter and no `/deployments/{id}` path segment. The target model is passed in the request
body (`model` field) instead. The dated `2024-10-21` surface above is unchanged and remains
available side by side.

```php
$v1 = Azure::instance()->openAi('my-aif')->v1();

$v1->chatCompletions([
    'model' => 'gpt-5-mini',
    'messages' => [['role' => 'user', 'content' => 'Hello']],
]);

$v1->embeddings(['model' => 'embed-model', 'input' => 'text']);
$v1->responses(['model' => 'gpt-5-mini', 'input' => 'Hello']);
$v1->models();
```

| Operation | Path | Tier |
| --- | --- | --- |
| Chat completions | `POST /openai/v1/chat/completions` | required |
| Embeddings | `POST /openai/v1/embeddings` | required |
| Responses API | `POST /openai/v1/responses` | required |
| List models | `GET /openai/v1/models` | required |
| Files | `GET/POST/DELETE /openai/v1/files` | extended |
| Image generation | `POST /openai/v1/images/generations` | extended |
| Speech / transcription | `POST /openai/v1/audio/speech`, `.../transcriptions` | extended |
| Fine-tuning jobs | `POST /openai/v1/fine_tuning/jobs` | extended |

---

## 7. Foundry Agent Service

Project-scoped agents, conversations, Responses API, MCP tool registration (toolboxes), connections, and skills. Legacy Assistants (threads/runs) marked **deprecated** (sunset Aug 2026).

```php
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

// Hosted-agent container lifecycle (needs Foundry-Features: HostedAgents=V1Preview)
$container = $foundry->withFoundryFeatures([FoundryFeature::HostedAgents])
    ->agent('hosted-agent')->version('3')->container();
$container->update(['image' => 'myregistry.azurecr.io/agent:v2']);
$container->awaitContainerOperation($operationId);

// Toolboxes — register an MCP server and any built-in tools (needs Foundry-Features: Toolboxes=V1Preview)
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

// Connections — auth/secrets for MCP and other tools
$foundry->connections()->create(['name' => 'docuware-mcp-conn', 'kind' => 'remote-tool', 'auth_type' => 'custom-keys']);

// Skills — versioned domain knowledge
$foundry->skills()->createVersion('doc-review', ['description' => 'Document review skill']);
```

| Operation | Path | Tier |
| --- | --- | --- |
| Agents CRUD + versions | `/agents`, `/agents/{name}/versions` | required |
| Agent update / replace | `POST /agents/{name}`, `PUT /agents/{name}` | required |
| Agent version routing (canary) | `PATCH /agents/{name}` | required (preview: `AgentEndpoints=V1Preview`) |
| Hosted-agent container ops | `PUT .../versions/{version}/container`, `.../containerOperations` | required (preview: `HostedAgents=V1Preview`) |
| Agent endpoint protocols | `/agents/{name}/endpoint/protocols/openai/responses`, `.../invocations` | required |
| Conversations (full CRUD + items + compact) | `/conversations`, `/conversations/{id}/items`, `.../compact` | required |
| Response lifecycle | `POST /responses`, `GET/DELETE /responses/{id}`, `POST .../cancel`, `GET .../input_items` | required |
| Toolboxes CRUD + versions | `/toolboxes`, `/toolboxes/{name}/versions` | required (preview: `Toolboxes=V1Preview` on every call) |
| Toolboxes MCP test/invoke | `POST /toolboxes/{name}/versions/{version}/mcp` | extended (preview: `Toolboxes=V1Preview`) |
| Connections CRUD | `/connections`, `/connections/{id}` | required |
| Skills versions + default switch | `/skills`, `/skills/{name}/versions`, `PATCH /skills/{name}` | required |
| Threads / runs (legacy) | `/threads`, `/threads/{id}/runs` | deprecated |

**Note:** the MCP server referenced by a Toolbox `server_url` is registered, not provisioned — this package does not deploy MCP servers, matching its `functionApps()`/`storageQueue()` scope of managing Azure resources rather than third-party app code. Connections responses may contain credential material; this package does not redact success-path response bodies (see `src/Security/Redactor.php`, which only scrubs error summaries).

Microsoft docs: [Foundry Agent Service](https://learn.microsoft.com/en-us/azure/foundry/agents/overview)

**Note:** Agent Framework `WorkflowBuilder` graph authoring is .NET/Python SDK-only. This package exposes the HTTP APIs only.

**Note:** for the OpenAI-compatible, account-scoped variant of response generation (`POST /openai/v1/responses`), use `Azure::instance()->openAi($account)->v1()->responses([...])` (§5) — a distinct surface from the project-scoped `foundry(...)->responses()` above.

The following six sub-surfaces are lower-priority/preview additions (all `tier: extended` in the inventory except where noted) — full CRUD support, but expect rougher edges than the core surface above.

### 7.1 Memory Stores

Knowledge/memory persistence. Uses `api-version=2025-11-15-preview`, distinct from the rest of Foundry (`v1`).

```php
$memoryStores = $foundry->memoryStores();
$memoryStores->create(['name' => 'support-memory']);
$memoryStores->update('ms-1', ['conversation_id' => 'conv-1']); // async — poll get() for completion
$memoryStores->search('ms-1', ['query' => 'last order status']);
```

| Operation | Path |
| --- | --- |
| Create / list / get / delete | `POST/GET /memory_stores`, `GET/DELETE /memory_stores/{id}` |
| Update (extract memories, async) | `PATCH /memory_stores/{id}` |
| Search | `POST /memory_stores/{id}/search` |

### 7.2 Evaluations

Standard CRUD, `api-version=v1`.

```php
$foundry->evaluations()->create(['name' => 'weekly-quality-check']);
$foundry->evaluations()->list();
```

| Operation | Path |
| --- | --- |
| Create / list / get / update / delete | `/evaluations`, `/evaluations/{id}` |

### 7.3 Schedules

Alternative/complementary trigger source to Azure Functions, with nested run history.

```php
$schedules = $foundry->schedules();
$schedules->createOrUpdate('daily-digest', ['cron' => '0 8 * * *']);
$schedules->runs('daily-digest')->list();
```

| Operation | Path |
| --- | --- |
| Create/update (upsert) / list / get / delete | `PUT /schedules/{id}`, `GET /schedules`, `GET/DELETE /schedules/{id}` |
| Run history | `schedules()->runs($id)->list()` / `->get($runId)` → `GET /schedules/{id}/runs[/{runId}]` |

### 7.4 Datasets

Version-scoped custom search/knowledge sources. **No list/discovery endpoint exists** — dataset names must already be known.

| Operation | Path |
| --- | --- |
| Create/update version (upsert) / get / delete | `PUT/GET/DELETE /datasets/{name}/versions/{version}` |

### 7.5 Indexes

Structurally identical to Datasets (separate resource, same limitation — no list endpoint).

| Operation | Path |
| --- | --- |
| Create/update version (upsert) / get / delete | `PUT/GET/DELETE /indexes/{name}/versions/{version}` |

### 7.6 Redteam

Safety/security test runs. **Create/list/get only** — no update or delete in the API (audit-log-style, assumed immutable by design).

| Operation | Path |
| --- | --- |
| Create / list / get | `POST/GET /redteams`, `GET /redteams/{name}` |

---

## 8. Function runtime — Agent Framework workflows

HTTP endpoints auto-generated when a Function App uses `ConfigureDurableWorkflows` (MAF durable extension).

```php
$runtime = Azure::instance()->functionRuntime('my-func', hostKey: $key);

$runtime->agents()->run('MyDurableAgent', ['input' => $payload]);
$runtime->workflows()->run('FlowRunner', ['input' => $payload]);
$runtime->workflows()->status('FlowRunner', $runId);
$runtime->workflows()->respond('FlowRunner', $runId, ['approved' => true]);
```

| Method | Path |
| --- | --- |
| POST | `/api/agents/{agentName}/run` |
| POST | `/api/workflows/{workflowName}/run` |
| GET | `/api/workflows/{workflowName}/status/{runId}` |
| POST | `/api/workflows/{workflowName}/respond/{runId}` |

Microsoft docs: [Durable workflows in Agent Framework](https://learn.microsoft.com/en-us/agent-framework/integrations/azure-functions)

---

## 9. Log Analytics KQL query (data plane)

Run Kusto Query Language (KQL) queries against a Log Analytics workspace. Base URL
`https://api.loganalytics.azure.com/v1`, auth scope `https://api.loganalytics.io/.default`.

Workspace-based Application Insights resources (the default kind since 2019) are queried
through this same endpoint using their linked workspace's customer ID — there is no separate
Application Insights query surface in this package.

```php
$results = Azure::instance()->logAnalytics()->query(
    workspaceId: $workspaceCustomerId, // the workspace's "Log Analytics customer ID"
    kql: 'AppRequests | where TimeGenerated > ago(1h) | take 50',
);

$results->table()?->rowsAssoc(); // list<array<string, mixed>>
```

| Operation | Path | Tier |
| --- | --- | --- |
| Execute query | `POST /workspaces/{workspaceId}/query` | required |

Microsoft docs: [Log Analytics query REST](https://learn.microsoft.com/en-us/rest/api/loganalytics/dataaccess/query/get)

---

## 10. Resource provisioning, monitoring & cost (ARM)

Full per-resource CRUD so the base stack can be composed via REST (no Bicep), plus
read surfaces for billing and metrics. All ARM-scoped (`management.azure.com`).

| Surface | Gateway |
| --- | --- |
| Storage accounts + keys | `storageAccounts($sub, $rg)->account($name)` → `get / createOrUpdate / delete / listKeys / regenerateKey` |
| Blob containers + lifecycle | `storageAccounts($sub, $rg)->account($name)->blobContainers()` → `createOrUpdate / setManagementPolicy` |
| Key Vault vaults (control plane) | `vaults($sub, $rg)->vault($name)` → `get / createOrUpdate / delete` |
| SQL servers | `sqlServers($sub, $rg)->server($name)` → `get / createOrUpdate / delete` |
| SQL databases | `sqlDatabases($sub, $rg, $server)` → `get / createOrUpdate / delete` |
| Log Analytics workspaces | `logAnalyticsWorkspaces($sub, $rg)->workspace($name)` → `get / createOrUpdate / delete` |
| Application Insights | `applicationInsights($sub, $rg)->component($name)` → `get / createOrUpdate / delete` |
| User-assigned managed identity | `managedIdentities($sub, $rg)->identity($name)` → `get / createOrUpdate / delete` |
| Cost Management (actual spend) | `costManagement($scope)->query($from, $to, $grouping)` |
| Consumption usage details | `consumption($scope)->usageDetails($filter)` *(paginated)* |
| Azure Monitor metrics | `metrics($resourceId)->get($names, $timespan, $interval, $aggregation)` / `definitions()` |

Long-running operations: `deployments($sub, $rg)->await($name)` and
`subscriptionAliases()->await($name)` poll provisioning state until terminal
(see `HandlesLongRunningOperations`). Deployment template outputs are exposed via
`DeploymentData::output($name)`.

---

## 11. Key Vault, Graph, Kudu

Unchanged from prior releases — see [README](README.md) usage examples.

| Surface | Gateway |
| --- | --- |
| Key Vault secrets | `vault($name)->secrets()` |
| Microsoft Graph | `graph()->groups()`, `users()`, `invitations()` |
| Kudu zip deploy | `appService($name)->zipDeploy($zipPath)` |

---

## 12. Storage Queue data plane

Enqueue, receive, and delete messages against an existing Storage Account queue. Message bodies are base64-encoded automatically (Azure rejects raw XML-unsafe markup in the message body). API version `2025-05-05`.

```php
$queue = Azure::instance()->storageAccounts($sub, $rg)->account('myacct')->queue('orders');

$queue->sendMessage('hello world', visibilityTimeoutSeconds: 0, messageTtlSeconds: 3600);
$messages = $queue->receiveMessages(numberOfMessages: 5);
$queue->deleteMessage($messages->first()->messageId, $messages->first()->popReceipt);

// Shared Key alternative — no RBAC assignment needed, uses an account key from listKeys()
$key = Azure::instance()->storageAccounts($sub, $rg)->account('myacct')->listKeys()->keys[0]['value'];
$queue = Azure::instance()->storageAccounts($sub, $rg)->account('myacct')->queue('orders', accountKey: $key);
```

| Operation | REST path |
| --- | --- |
| Send message | `POST /{queue}/messages` |
| Receive messages | `GET /{queue}/messages` |
| Delete message | `DELETE /{queue}/messages/{messageId}` |

**Two distinct auth modes, chosen per call via the optional `$accountKey` argument on `queue()`:**
- Omit it → Entra ID bearer token (`https://storage.azure.com/.default`); the caller's service principal needs the `Storage Queue Data Contributor` RBAC role.
- Pass an account key (from `listKeys()`) → Shared Key (Full) request signing, computed per-request via HMAC-SHA256 over the canonicalized headers and resource — see `SharedKeyAuthenticator`.

**Note for maintainers:** this is the package's first data-plane gateway to (a) support Shared Key/HMAC signing rather than a bearer token, and (b) parse XML responses instead of JSON — both genuinely new code paths, isolated to `SharedKeyAuthenticator`, `StorageQueueConnector`, and `XmlField`. Scope stays intentionally narrow (message send/receive/delete only — no queue management, peek, or update-message operations).

Microsoft docs: [Storage Queue REST](https://learn.microsoft.com/en-us/rest/api/storageservices/queue-service-rest-api), [Authorize with Shared Key](https://learn.microsoft.com/en-us/rest/api/storageservices/authorize-with-shared-key)

---

## Regenerating docs

After adding or changing Requests:

```bash
composer docs:api
composer inventory:parity
```

CI enforces inventory parity on every push.
