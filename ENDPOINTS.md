# Endpoint catalog

Human-readable index of every REST surface in `laravel-microsoft-azure`. For class-level mappings see [docs/api-reference.md](docs/api-reference.md) (auto-generated). For parity status see [docs/inventory-parity.md](docs/inventory-parity.md).

**112 Saloon request classes** across **9 surfaces**. All wrappers are REST-only — no .NET SDK or Agent Framework runtime code in this package.

## Authentication scopes

| Surface | Base URL | Auth |
| --- | --- | --- |
| ARM / Functions ARM / Foundry control plane | `https://management.azure.com` | Entra client credentials — `https://management.azure.com/.default` |
| Key Vault | `https://{vault}.vault.azure.net` | Entra — `https://vault.azure.net/.default` |
| Microsoft Graph | `https://graph.microsoft.com/v1.0` | Entra — `https://graph.microsoft.com/.default` |
| Kudu (SCM) | `https://{app}.scm.azurewebsites.net` | Entra — scoped to SCM host |
| Azure OpenAI data plane | `https://{account}.openai.azure.com` | Entra — `https://cognitiveservices.azure.com/.default` **or** `api-key` header |
| Foundry Agent Service | `https://{account}.services.ai.azure.com/api/projects/{project}` | Same as OpenAI data plane |
| Function runtime | `https://{app}.azurewebsites.net` | `x-functions-key` / host key **or** Entra scoped to app host |
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

Manage AI Services accounts, Foundry projects, and model deployments. API version `2025-06-01`.

```php
$cs = Azure::instance()->cognitiveServices($sub, $rg);

$cs->account('my-aif')->createOrUpdate('westeurope', [
    'customSubDomainName' => 'my-aif',
    'disableLocalAuth' => false,
    'allowProjectManagement' => true,
]);

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

Function App lifecycle, app settings, host/function keys, trigger sync. API version `2023-12-01`.

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

## 4. Azure OpenAI data plane

Inference and file APIs against `{account}.openai.azure.com`. Supports Entra or API key auth.

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

---

## 5. Foundry Agent Service

Project-scoped agents, conversations, and Responses API. Legacy Assistants (threads/runs) marked **deprecated** (sunset Aug 2026).

```php
$foundry = Azure::instance()->foundry('my-aif', 'my-prj');

$foundry->agents()->create(['name' => 'doc-agent', 'definition' => [...]]);
$foundry->conversations()->create([]);
$foundry->responses()->create(['model' => 'gpt-5-mini', 'input' => 'Run step']);
```

| Operation | Path | Tier |
| --- | --- | --- |
| Agents CRUD + versions | `/agents`, `/agents/{name}/versions` | required |
| Conversations | `/conversations`, `/conversations/{id}/items` | required |
| Project responses | `/responses` | required |
| Threads / runs (legacy) | `/threads`, `/threads/{id}/runs` | deprecated |

Microsoft docs: [Foundry Agent Service](https://learn.microsoft.com/en-us/azure/foundry/agents/overview)

**Note:** Agent Framework `WorkflowBuilder` graph authoring is .NET/Python SDK-only. This package exposes the HTTP APIs only.

---

## 6. Function runtime — Agent Framework workflows

HTTP endpoints auto-generated when a Function App uses `ConfigureDurableWorkflows` (MAF durable extension).

```php
$runtime = Azure::instance()->functionRuntime('my-func', hostKey: $key);

$runtime->workflows()->run('FlowRunner', ['input' => $payload]);
$runtime->workflows()->status('FlowRunner', $runId);
$runtime->workflows()->respond('FlowRunner', $runId, ['approved' => true]);
```

| Method | Path |
| --- | --- |
| POST | `/api/workflows/{workflowName}/run` |
| GET | `/api/workflows/{workflowName}/status/{runId}` |
| POST | `/api/workflows/{workflowName}/respond/{runId}` |

Microsoft docs: [Durable workflows in Agent Framework](https://learn.microsoft.com/en-us/agent-framework/integrations/azure-functions)

---

## 7. Key Vault, Graph, Kudu

Unchanged from prior releases — see [README](README.md) usage examples.

| Surface | Gateway |
| --- | --- |
| Key Vault secrets | `vault($name)->secrets()` |
| Microsoft Graph | `graph()->groups()`, `users()`, `invitations()` |
| Kudu zip deploy | `appService($name)->zipDeploy($zipPath)` |

---

## Regenerating docs

After adding or changing Requests:

```bash
composer docs:api
composer inventory:parity
```

CI enforces inventory parity on every push.
