# Configuration

Published to `config/laravel-microsoft-azure.php`. All values are read at call time via Laravel's `config()` helper — no caching beyond what Laravel's config cache normally does.

## Connections (multi-tenant)

Every credential and per-connection setting lives under a named key in `connections`. The published config only defines `connections.default`, but you can add more (e.g. `connections.tenant_a`, `connections.tenant_b`) for multi-tenant apps, each with its own tenant/client/secret/subscription:

```php
'connections' => [
    'default' => [
        'tenant_id' => env('MICROSOFT_AZURE_TENANT_ID'),
        // ...
    ],
    'tenant_a' => [
        'tenant_id' => env('MICROSOFT_AZURE_TENANT_A_TENANT_ID'),
        'client_id' => env('MICROSOFT_AZURE_TENANT_A_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_AZURE_TENANT_A_CLIENT_SECRET'),
        'subscription_id' => env('MICROSOFT_AZURE_TENANT_A_SUBSCRIPTION_ID'),
    ],
],
```

Reach a non-default connection with `Azure::instance('tenant_a')`, or pass an explicit `ConnectionConfig` to `Azure::connection($config)`. See [Advanced usage](advanced.md).

## Env var reference

| Env var | Config path | Default | Required | Description |
|---|---|---|---|---|
| `MICROSOFT_AZURE_CONNECTION` | `default` | `default` | No | Name of the active connection from the `connections` map. |
| `MICROSOFT_AZURE_TENANT_ID` | `connections.default.tenant_id` | — | **Yes** | Azure AD tenant ID used for the client-credentials OAuth2 flow. |
| `MICROSOFT_AZURE_CLIENT_ID` | `connections.default.client_id` | — | **Yes** | App registration (service principal) client ID. |
| `MICROSOFT_AZURE_CLIENT_SECRET` | `connections.default.client_secret` | — | **Yes** | App registration client secret. Never commit this — see [Security](#security). |
| `MICROSOFT_AZURE_SUBSCRIPTION_ID` | `connections.default.subscription_id` | — | For ARM calls | Default ARM subscription ID passed into subscription-scoped gateways, e.g. `resourceGroups($subId)`. |
| `MICROSOFT_AZURE_CACHE_DRIVER` | `connections.default.cache_driver` | `file` | No | Laravel cache store used to cache per-audience OAuth tokens (stored encrypted). |
| `MICROSOFT_AZURE_CACHE_LIFETIME_IN_SECONDS` | `connections.default.cache_lifetime_in_seconds` | `3300` | No | ⚠️ **Currently has no effect** — see [Known drift](#known-drift-cache-lifetime--request-timeout) below. |
| `MICROSOFT_AZURE_REQUEST_TIMEOUT_IN_SECONDS` | `connections.default.request_timeout_in_seconds` | `60` | No | ⚠️ **Currently has no effect** — see [Known drift](#known-drift-cache-lifetime--request-timeout) below. |
| `MICROSOFT_AZURE_TIMEOUT` | `timeout` (top-level) | `60` | No | ⚠️ **Currently has no effect** — see [Known drift](#known-drift-cache-lifetime--request-timeout) below. |
| `MICROSOFT_AZURE_RETRY_ENABLED` | `retry.enabled` | `true` | No | Enables Saloon's automatic retry-on-failure behavior for idempotent requests (`GET`/`HEAD`/`PUT`/`DELETE`/`OPTIONS`) and any `429`/`5xx` response. |
| `MICROSOFT_AZURE_RETRY_TIMES` | `retry.times` | `3` | No | Max retry attempts. |
| `MICROSOFT_AZURE_RETRY_BASE_INTERVAL_MS` | `retry.base_interval_ms` | `250` | No | Base exponential-backoff interval between retries, in milliseconds. |
| `MICROSOFT_AZURE_RETRY_MAX_INTERVAL_MS` | `retry.max_interval_ms` | `10000` | No | Cap applied to the backoff interval computed from a `429` response's `Retry-After` header. |
| `MICROSOFT_AZURE_RATE_LIMIT_ENABLED` | `rate_limit.enabled` | `false` | No | Enables client-side rate limiting via Saloon's rate-limit plugin, backed by the connection's cache driver. Can also be overridden per-connection at `connections.{name}.rate_limit.*`. |
| `MICROSOFT_AZURE_RATE_LIMIT_ALLOW` | `rate_limit.allow` | `60` | No | Max requests allowed per window. |
| `MICROSOFT_AZURE_RATE_LIMIT_PER_SECONDS` | `rate_limit.per_seconds` | `60` | No | Rate-limit window length, in seconds. |
| `MICROSOFT_AZURE_DEBUG_CAPTURE_BODIES` | `debug.capture_bodies` | `false` | No | When enabled, the `AzureResponseReceived` event carries the redacted raw response body. Keep off in production; response bodies from some surfaces (e.g. connections/credentials data) aren't redacted on success paths — see [Limitations](limitations.md). |

### Known drift: cache lifetime & request timeout

> **Known drift (needs maintainer confirm/fix):** `MICROSOFT_AZURE_CACHE_LIFETIME_IN_SECONDS`, `MICROSOFT_AZURE_REQUEST_TIMEOUT_IN_SECONDS`, and the top-level `MICROSOFT_AZURE_TIMEOUT` are **not read by the code that resolves connection settings**. `MicrosoftAzureManager::connectionAttributes()` (`src/MicrosoftAzureManager.php:89-97`) looks for `$connection['cache_lifetime']` and `$connection['timeout']`, falling back to `laravel-microsoft-azure.cache.lifetime_in_seconds` / `laravel-microsoft-azure.request.timeout_in_seconds` — none of which match the keys actually published in `config/laravel-microsoft-azure.php` (`cache_lifetime_in_seconds`, `request_timeout_in_seconds`, and a flat top-level `timeout`). As a result, these three env vars currently have no effect: the effective cache lifetime and request timeout always fall through to `ConnectionConfig`'s hardcoded defaults (3600s cache, 60s request timeout), regardless of what you set. Setting them will not raise an error — they're silently ignored. This is called out here rather than fixed as part of a docs-only change; if you need non-default values today, the only way is to change the `ConnectionConfig::DEFAULT_*` constants directly or patch `connectionAttributes()`.

## Security

- Never commit `MICROSOFT_AZURE_CLIENT_SECRET` or any account/API key (Storage Shared Key, API Management subscription key, OpenAI API key) to source control.
- Prefer the Entra ID (OAuth) auth path over Shared Key / API-key auth where a surface offers both (see [Storage](usage/storage.md) and [Foundry & OpenAI](usage/foundry-and-openai.md)) — it avoids long-lived static secrets and is scoped by RBAC instead of a bearer key.
- `debug.capture_bodies` logs raw response bodies through the `AzureResponseReceived` event. `src/Security/Redactor.php` only scrubs known secret-shaped fields in **error** summaries — success-path bodies captured this way are not redacted, and some surfaces (e.g. Foundry connections) can return credential material in a 200 response. Leave this off outside of local debugging.
