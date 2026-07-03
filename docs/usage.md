# Usage

Everything goes through the `Azure` facade (`CodebarAg\MicrosoftAzure\Facades\Azure`), backed by the `MicrosoftAzureManager` singleton. Resolve a connection once, then chain fluent **Resource gateway** methods per Azure service:

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

Azure::instance()->resourceGroups($subscriptionId)->get('my-rg');
Azure::instance()->vault('my-kv')->secrets()->get('webhook-token');
Azure::instance()->foundry('my-aif', 'my-prj')->agents()->create([...]);
```

`Azure::instance()` returns an `AzureClient` for the default connection (or a named one, or an explicit `ConnectionConfig` — see [Advanced usage](advanced.md#multi-tenant--multiple-connections)). Every gateway method on it returns another Resource gateway or a typed DTO/`Collection` of DTOs — never a raw array.

If a call fails, it throws a typed exception (`BadRequestException`, `AuthenticationException`, etc.) — see [Troubleshooting](troubleshooting.md).

## Surfaces

| Page | Covers |
|---|---|
| [ARM core](usage/arm-core.md) | Subscriptions, subscription aliases (billing/MCA/EA), resource groups, deployments, role assignments/definitions, resource providers |
| [Key Vault](usage/key-vault.md) | ARM vault management + data-plane secrets |
| [Storage](usage/storage.md) | Storage accounts, keys, blob containers, and Storage Queue data plane (Entra + Shared Key) |
| [SQL](usage/sql.md) | SQL servers, databases, firewall rules, AAD token auth |
| [Foundry & Azure OpenAI](usage/foundry-and-openai.md) | Cognitive Services/Foundry control plane, Azure OpenAI data plane (dated + v1) |
| [Foundry Agent Service](usage/foundry-agent-service.md) | Agents, conversations, responses, connections, toolboxes/MCP, and the smaller preview sub-surfaces (memory stores, evaluations, schedules, datasets, indexes, redteams) |
| [Logic Apps & API Management](usage/logic-apps-and-apim.md) | Logic Apps workflows/runs/triggers, APIM subscriptions and keys |
| [Functions & Web Apps](usage/functions-and-web-apps.md) | Azure Functions ARM, App Service, Kudu zip-deploy, Function Runtime, managed identities |
| [Observability & cost](usage/observability-and-cost.md) | Application Insights, Monitor metrics, Log Analytics (KQL), Consumption, Cost Management |
| [Microsoft Graph](usage/graph.md) | Applications, groups, invitations, service principals, users |

## API reference

For the exhaustive, auto-generated, class-level view of every request/DTO behind these pages:

- [`ENDPOINTS.md`](../ENDPOINTS.md) — human-curated endpoint catalog, grouped by surface, with REST path tables
- [`docs/api-reference.md`](api-reference.md) — every Saloon request class, response DTO, and write payload (regenerate with `composer docs:api`)
- [`docs/inventory-parity.md`](inventory-parity.md) — endpoint coverage vs. Saloon request classes (regenerate with `composer inventory:parity`)

CI keeps both generated files in sync with `src/Requests/` — don't hand-edit them.
