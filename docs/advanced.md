# Advanced usage

## Multi-tenant / multiple connections

Beyond the single `connections.default` entry in the published config, you can add more named connections (see [Configuration](configuration.md#connections-multi-tenant)) and reach them explicitly:

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

Azure::instance('tenant_a')->resourceGroups($subscriptionId)->list();
```

Or bypass config entirely and pass a `ConnectionConfig` built at runtime (e.g. per-request tenant credentials loaded from your own database):

```php
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;

$config = ConnectionConfig::make('runtime-tenant', [
    'tenantId' => $tenant->azure_tenant_id,
    'clientId' => $tenant->azure_client_id,
    'clientSecret' => $tenant->azure_client_secret,
    'subscriptionId' => $tenant->azure_subscription_id,
]);

Azure::connection($config)->resourceGroups($tenant->azure_subscription_id)->list();
```

`Azure::instance($name)` caches one `AzureClient` per connection name for the life of the request; `Azure::connection($config)` always builds a fresh, uncached client — use it when credentials are dynamic per call rather than fixed in config. Clear a cached client with `Azure::forget($name)` (or `Azure::forget()` for all).

## Retry, rate limiting, and debug capture

All three are configured per the [env vars in Configuration](configuration.md#env-var-reference) and applied by `Concerns/ConfiguresAzureTransport` on every Azure connector:

- **Retry** (`retry.*`) — Saloon automatic retry with exponential backoff, applied to idempotent methods (`GET`/`HEAD`/`PUT`/`DELETE`/`OPTIONS`) on any failure, and to *any* method on a `429` (honoring the response's `Retry-After` header, capped at `retry.max_interval_ms`).
- **Rate limiting** (`rate_limit.*`) — an optional client-side limiter (Saloon's rate-limit plugin) backed by the connection's cache driver, keyed per connection name. Can be set globally or overridden per connection at `connections.{name}.rate_limit.*`.
- **Debug capture** (`debug.capture_bodies`) — when enabled, the `AzureResponseReceived` event includes the redacted response body. See the redaction caveat in [Limitations](limitations.md).

## Long-running operation (LRO) polling

`Concerns/HandlesLongRunningOperations` is mixed into `DeploymentsResource` and `SubscriptionAliasesResource` and gives two polling strategies:

**`awaitProvisioningState()`** — re-reads a DTO via a callback until its `provisioningState` is terminal:

```php
// DeploymentsResource::await() wraps awaitProvisioningState() internally:
$dep = Azure::instance()
    ->deployments($subscriptionId, $resourceGroup)
    ->await('tenantflow', timeoutSeconds: 600, intervalSeconds: 5);

// SubscriptionAliasesResource::await() does the same for subscription-alias creation:
$alias = Azure::instance()->subscriptionAliases()->await('tenant-acme');
```

Under the hood, `await()` just calls `awaitProvisioningState(fetch: fn () => $this->get($name), ...)` and returns the terminal DTO. Throws `LongRunningOperationException` if the resource reaches `Failed`/`Canceled`, or if `timeoutSeconds` elapses without reaching a terminal state.

**`awaitAsyncOperation()`** — follows the `Azure-AsyncOperation`/`Location` header on a `201`/`202` response, honoring `Retry-After`, and returns the terminal operation body as an array. Used internally wherever a create call returns `202 Accepted` rather than embedding `provisioningState` in the immediate response.

Both accept an optional `onTick` callback invoked on every poll — useful for logging/progress reporting during a long deploy.

## Actual-state reads (`observe()`)

`get()` answers "give me this resource" and throws when it cannot — correct for a caller
that already knows the resource is there. A caller reconciling desired state against actual
state needs the opposite: `404` and `403` *are* the interesting answers, and wrapping every
read in `try`/`catch` at the call site is how "someone deleted it in the portal" becomes
invisible.

`Concerns/ObservesActualState` adds an `observe()` alongside `get()` on the provisionable
families. It returns a `Data\Observation\ResourceObservationData` and only throws for
failures that say nothing about the resource (400, 429, 5xx).

```php
use CodebarAg\MicrosoftAzure\Enums\ObservedState;

$observation = Azure::instance()
    ->vaults($subscriptionId, 'my-rg')
    ->vault('my-kv')
    ->observe(location: 'westeurope');

match ($observation->state) {
    ObservedState::Absent       => $this->create(),
    ObservedState::SoftDeleted  => $this->purgeThenCreate(),   // the name is held; waiting will not help
    ObservedState::Provisioning => $this->checkAgainLater(),
    ObservedState::Available    => $this->use($observation->resource),
    ObservedState::Failed       => $this->deleteAndRecreate(),
    ObservedState::Deleting     => $this->checkAgainLater(),
    ObservedState::Inaccessible => $this->checkAgainLater(),   // usually an RBAC grant still propagating
};
```

### The state vocabulary

| State | Meaning | `isUsable` | `isPresent` | `blocksCreate` | `isSettled` |
| --- | --- | --- | --- | --- | --- |
| `Absent` | 404, and nothing holds the name | no | no | no | yes |
| `Provisioning` | present, `provisioningState` not terminal | no | yes | no | no |
| `Available` | present, provisioned, readable by us | **yes** | yes | no | yes |
| `Failed` | present, `Failed`/`Canceled` — will not recover | no | yes | no | yes |
| `Deleting` | present, on its way out | no | yes | **yes** | no |
| `SoftDeleted` | deleted, not purged — the name is still held | no | no | **yes** | yes |
| `Inaccessible` | 403 — existence unknown | no | no | no | no |

Three distinctions this exists to preserve, each of which produces a specific bug when collapsed:

- **`Provisioning` vs `Available`.** A resource exists long before it works. A read that only
  answers "exists" makes a caller declare success over something nothing can talk to yet.
  Storage accounts sit in `ResolvingDNS` and Key Vaults in `RegisteringDns` for exactly this
  window.
- **`Inaccessible` vs `Absent`.** ARM answers **403**, not 404, when the calling principal has
  no read permission — and keeps doing so for tens of seconds after a role assignment is
  granted. A 403 says nothing about existence, so `observe()` refuses to guess. Both plausible
  guesses are destructive: "absent" provokes a duplicate create, "available" hands out a
  resource that cannot be reached.
- **`SoftDeleted` vs `Absent`.** A deleted Key Vault holds its name for its retention window; a
  deleted Cognitive Services / Foundry account holds its name **and** its subscription-wide
  model quota. ARM `409`s every create against a held name and no amount of polling frees it —
  only a purge does. Reported as absent, a caller retries the create forever. Pass `$location`
  to `observe()` on those two families to enable the probe; without it the deleted-resource
  surface (which is scoped per location) cannot be queried and the state is reported as absent.

If the soft-delete probe itself is refused, `observe()` returns `Inaccessible` rather than
`Absent` — "we could not determine" beats a guess that provokes a create against a held name.

### Unrecognised provisioning states

`ResourceObservationData` derives its state from the raw `properties.provisioningState`
string, not from the typed DTO, and keeps that string on `reportedProvisioningState`. A value
`ProvisioningState` has no case for leaves `provisioningState` null and maps to
`Provisioning` — never to `Available`. A provider adding a new lifecycle state can therefore
slow a caller down, but cannot make it hand out an unusable resource.

### Control plane vs data plane

`observe()` reads the control plane. `Available` means ARM has provisioned the resource — not
that its data plane will serve you. Key Vault enforces its own RBAC, and a freshly granted
role takes tens of seconds to reach it: in that window the vault is `Available` on ARM and
every secret call 403s. `secrets()->observeAccess()` asks that second question directly
(it lists secret identifiers, never values).

## Pagination

Azure list endpoints that page via `nextLink` / `@odata.nextLink` (e.g. Consumption usage details, Graph list endpoints) are followed transparently inside the relevant `Resource` method using an internal `mapPaginated()` helper (capped at 100 pages as a runaway guard) — you get back a fully-materialized `Illuminate\Support\Collection`, not a page at a time. There's nothing extra to call; if a `list()`/`usageDetails()`-style method returns a `Collection`, pagination already happened.

## Facade vs. manager

`Azure::instance()` (facade) and `app(MicrosoftAzureManager::class)->instance()` are equivalent — the facade is a thin accessor over the `MicrosoftAzureManager` singleton bound by `MicrosoftAzureServiceProvider`. Use dependency injection instead of the facade wherever you'd normally prefer it in your app (e.g. constructor-injecting `MicrosoftAzureManager` for testability).
