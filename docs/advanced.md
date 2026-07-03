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

## Pagination

Azure list endpoints that page via `nextLink` / `@odata.nextLink` (e.g. Consumption usage details, Graph list endpoints) are followed transparently inside the relevant `Resource` method using an internal `mapPaginated()` helper (capped at 100 pages as a runaway guard) — you get back a fully-materialized `Illuminate\Support\Collection`, not a page at a time. There's nothing extra to call; if a `list()`/`usageDetails()`-style method returns a `Collection`, pagination already happened.

## Facade vs. manager

`Azure::instance()` (facade) and `app(MicrosoftAzureManager::class)->instance()` are equivalent — the facade is a thin accessor over the `MicrosoftAzureManager` singleton bound by `MicrosoftAzureServiceProvider`. Use dependency injection instead of the facade wherever you'd normally prefer it in your app (e.g. constructor-injecting `MicrosoftAzureManager` for testability).
