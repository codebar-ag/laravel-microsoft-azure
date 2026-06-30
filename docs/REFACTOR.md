# Full-REST refactor — what changed & why

This release extends `laravel-microsoft-azure` so the **Flows portal** can drive the entire
tenant provisioning lifecycle over REST (retiring the PowerShell CLI + standalone Bicep step).
Everything follows the existing CognitiveServices group/item gateway pattern: Saloon `Request` →
`AzurePayload::toAzureBody()` → `AzureData::fromAzure()`, fluent entry in
`InteractsWithResources`, `@method` in the `Azure` facade, ARM scope reused (no new token
audience).

## 1. Foundational (cross-cutting)
- **`Enums/ApiVersion`** — added constants: `ARM_STORAGE`, `ARM_KEY_VAULT_VAULTS`,
  `ARM_LOG_ANALYTICS`, `ARM_APP_INSIGHTS`, `ARM_MANAGED_IDENTITY`, `ARM_COST_MANAGEMENT`,
  `ARM_CONSUMPTION`, `ARM_MONITOR_METRICS`. (`ARM_SQL` reused for SQL server/db.)
- **`Concerns/HandlesLongRunningOperations`** — reusable LRO polling:
  - `awaitProvisioningState(fetch, timeout, interval, onTick)` — re-reads a DTO until
    `provisioningState->isTerminal()`; throws `LongRunningOperationException` on Failed/Canceled/timeout.
  - `awaitAsyncOperation(Response, …)` — follows the `Azure-AsyncOperation`/`Location` header,
    honoring `Retry-After`.
  - Mixed into `DeploymentsResource` (`->await($name)`) and `SubscriptionAliasesResource`
    (`->await($name)`), replacing the hand-rolled poll loops.
  - Test seams `now()` / `sleepSeconds()` (interval `0` = no real sleep in tests).
- **`Resources/Resource::mapPaginated()`** — follows `nextLink` / `@odata.nextLink` to the end
  (with a `maxPages` runaway guard). Absolute Azure URLs are normalized to a path+query endpoint
  relative to the ARM base via `Requests/Arm/Support/AbsoluteArmUrl` (Saloon rejects absolute
  endpoints; the raw query is preserved so Azure's `$skiptoken`/`$filter` survive). Same helper
  backs `PollAsyncOperation` and `GetNextPage`.
- **`Data/Arm/DeploymentData`** — now deserializes `properties.outputs` + `properties.error`;
  added `output(string $name)` so the portal reads webhook URL / SQL FQDN straight from the
  deployment result.

## 2. Read/observe surfaces (Pricing + progress)
- **Cost Management** — `costManagement($scope)->query($from, $to, $grouping = 'ServiceName')`
  (POST ActualCost query; zips columns→rows into `CostQueryResultData`).
- **Monitor metrics** — `metrics($resourceId)->get($names, $timespan, $interval, $aggregation)` and
  `->definitions()` (`MetricResultData`).
- **Consumption** — `consumption($scope)->usageDetails($filter)` (paginated via `mapPaginated`).

## 3. Per-resource CRUD (compose the stack without Bicep)
`storageAccounts` (accounts, keys, blob containers, lifecycle policy), `vaults` (Key Vault
control plane), `sqlServers` + extended `sqlDatabases` (create/delete), `logAnalyticsWorkspaces`,
`applicationInsights`, `managedIdentities`. `SqlDatabaseData` enriched additively with
`currentServiceObjectiveName` + `autoPauseDelay`.

## Out of scope (by design)
- **DB migrations + flow seeding** stay in the .NET runner/seeder (not an Azure-management
  concern). Deploy the schema via that runner or reimplement against the tenant SQL.
- **Service "Verify" health checks** (DocuWare / MCP / Mistral) live in the portal, not here.

## Surface map
See [`ENDPOINTS.md`](../ENDPOINTS.md) §7 for the new gateways. Regenerate generated docs with
`composer docs:api` and `composer inventory:parity` after any request change.

## Tests
New Pest coverage under `tests/Unit/Requests/*` (endpoint + api-version datasets) and
`tests/Unit/Resources/*` (MockClient gateway + DTO mapping), plus
`tests/Unit/Concerns/LongRunningOperationsTest` and `tests/Unit/Resources/FluentResourceEntriesTest`.
Suite: `composer test` (312 passing), `composer analyse` (PHPStan clean), `vendor/bin/pint`.
