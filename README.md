# laravel-microsoft-azure

[![Tests](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/run-tests.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/phpstan.yml)
[![Code Style](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/fix-php-code-style-issues.yml)
[![Composer Audit](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/composer-audit.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/composer-audit.yml)

Thin Azure and Microsoft 365 REST connector for Laravel — Saloon transport only, no business logic.

## Why

One `Azure` facade with fluent, typed Resource gateways over 13 Azure/Microsoft 365 REST surfaces — ARM (incl. Logic Apps, API Management), Azure AI Foundry (control + data plane, dated and v1 OpenAI surfaces), Azure Functions (ARM + runtime), Key Vault, Microsoft Graph, Log Analytics (KQL), Storage Queue (data plane), and Kudu. No official `microsoft/*` SDK dependency — every call is a hand-written Saloon request against the documented REST API. It does **not** do orchestration: provisioning sequences, retry policy beyond Saloon's built-in retry, and idempotency across multiple calls belong in the consuming app. See [Limitations](docs/limitations.md) for the full list of what's deliberately out of scope.

## Requirements

| | Supported |
|---|---|
| PHP | `8.4.*` or `8.5.*` |
| Laravel | `^13.0` |

No legacy PHP or Laravel support. Details: [docs/installation.md](docs/installation.md).

## Install

```bash
composer require codebar-ag/laravel-microsoft-azure
php artisan vendor:publish --tag=laravel-microsoft-azure-config
```

Service provider and `Azure` facade register automatically via Laravel package auto-discovery. Full steps and Azure prerequisites: [docs/installation.md](docs/installation.md).

## Configuration

```env
MICROSOFT_AZURE_TENANT_ID=
MICROSOFT_AZURE_CLIENT_ID=
MICROSOFT_AZURE_CLIENT_SECRET=
MICROSOFT_AZURE_SUBSCRIPTION_ID=
```

That's the minimum. The published config also supports multi-tenant connections, retry/rate-limit policy, cache driver, and debug capture — full reference (and a documented config-key drift you should know about): [docs/configuration.md](docs/configuration.md).

## Quick start

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

Azure::instance()->resourceGroups($subscriptionId)->list();
Azure::instance()->vault('my-keyvault')->secrets()->get('my-secret');
```

Runnable end-to-end walkthrough: [docs/quick-start.md](docs/quick-start.md).

## Usage

One facade, chained fluent gateways per Azure service — every call returns a typed DTO or `Collection`, never a raw array:

```php
Azure::instance()->vault('my-kv')->secrets()->set('webhook-token', $token);
Azure::instance()->foundry('my-aif', 'my-prj')->responses()->create(['model' => 'gpt-5-mini', 'input' => 'Hello']);
```

Full surface catalog, one page per group:

- [ARM core](docs/usage/arm-core.md) — subscriptions, resource groups, deployments, RBAC
- [Key Vault](docs/usage/key-vault.md)
- [Storage](docs/usage/storage.md) — accounts, blob containers, Storage Queue data plane
- [SQL](docs/usage/sql.md)
- [PostgreSQL](ENDPOINTS.md) — flexible servers, databases, firewall rules, server parameters (ARM `Microsoft.DBforPostgreSQL/flexibleServers`)
- [Foundry & Azure OpenAI](docs/usage/foundry-and-openai.md)
- [Foundry Agent Service](docs/usage/foundry-agent-service.md)
- [Logic Apps & API Management](docs/usage/logic-apps-and-apim.md)
- [Functions & Web Apps](docs/usage/functions-and-web-apps.md) — Functions, App Service, Kudu, managed identities
- [Observability & cost](docs/usage/observability-and-cost.md) — App Insights, Monitor, Log Analytics, Cost Management
- [Microsoft Graph](docs/usage/graph.md)

Class-level reference: [`ENDPOINTS.md`](ENDPOINTS.md) (curated catalog), [`docs/api-reference.md`](docs/api-reference.md) and [`docs/inventory-parity.md`](docs/inventory-parity.md) (auto-generated — regenerate with `composer docs:api` / `composer inventory:parity` after changing Requests, DTOs, or Resources).

## Advanced usage

Multi-tenant connections, long-running-operation polling, pagination, retry and rate-limit tuning: [docs/advanced.md](docs/advanced.md).

## Testing

Contributor-facing test suite, Saloon fixtures, and live-integration setup: [docs/testing.md](docs/testing.md).

## Troubleshooting

Error → cause → fix table for every exception this package throws: [docs/troubleshooting.md](docs/troubleshooting.md).

## Limitations

No business logic/orchestration, no DB migrations, no service health checks, no official SDK — full list: [docs/limitations.md](docs/limitations.md).

## Contributing

```bash
composer test      # offline unit + core tests
composer analyse    # PHPStan level 10
composer format      # Pint
```

If your change touches `Requests/`, `Data/`, or `Resources/`, regenerate the generated docs before opening a PR — CI checks both are in sync:

```bash
composer docs:api
composer inventory:parity
```

More detail on the test suite (fixtures, live integration tiers): [docs/testing.md](docs/testing.md).

## Support, license

- Issues & source: https://github.com/codebar-ag/laravel-microsoft-azure
- MIT, see [LICENSE](LICENSE)
