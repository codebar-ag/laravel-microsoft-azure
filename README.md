# laravel-microsoft-azure

[![Tests](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/run-tests.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/phpstan.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/phpstan.yml)
[![Code Style](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/fix-php-code-style-issues.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/fix-php-code-style-issues.yml)
[![Composer Audit](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/composer-audit.yml/badge.svg)](https://github.com/codebar-ag/laravel-microsoft-azure/actions/workflows/composer-audit.yml)

Thin Azure and Microsoft 365 REST connector for Laravel — Saloon transport only, no business logic.

Covers **ARM**, **Azure AI Foundry** (control + data plane), **Azure Functions** (ARM + runtime), **Key Vault**, **Microsoft Graph**, and **Kudu**. Orchestration (provisioning sequences, LRO polling, idempotency) belongs in the consuming app.

## Install

```bash
composer require codebar-ag/laravel-microsoft-azure
```

Publish config:

```bash
php artisan vendor:publish --tag=laravel-microsoft-azure-config
```

## Configuration

```env
MICROSOFT_AZURE_TENANT_ID=
MICROSOFT_AZURE_CLIENT_ID=
MICROSOFT_AZURE_CLIENT_SECRET=
MICROSOFT_AZURE_SUBSCRIPTION_ID=
```

## Usage

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$client = Azure::instance(); // AzureClient with ->config (ConnectionConfig)

// ARM
// ARM — subscriptions (read + cancel)
Azure::instance()->subscriptions()->list();
Azure::instance()->subscriptions()->get($subscriptionId);
Azure::instance()->subscriptions()->cancel($subscriptionId);

// ARM — create new subscriptions via billing-scope aliases (MCA / EA)
$alias = Azure::instance()->subscriptionAliases()->createOrUpdate(
    aliasName: 'tenant-acme',
    billingScope: '/providers/Microsoft.Billing/billingAccounts/{id}/enrollmentAccounts/{id}',
    displayName: 'Acme Tenant',
);
// Poll until $alias->provisioningState->isTerminal(), then use $alias->subscriptionId

Azure::instance()->resourceGroups($subscriptionId)->get('my-rg');
Azure::instance()->deployments($subscriptionId, 'my-rg')->createOrUpdate('tenantflow', $template, $params);

// Key Vault
Azure::instance()->vault('my-kv')->secrets()->set('webhook-token', $token);

// Graph
Azure::instance()->graph()->groups()->addMember($groupId, $userId);

// Kudu zip deploy (artifact built in CI)
Azure::instance()->appService('my-func')->zipDeploy('/path/to/intake.zip');

// Foundry control plane — deploy gpt-5-mini, rotate keys
$cs = Azure::instance()->cognitiveServices($subscriptionId, 'my-rg');
$cs->account('my-aif')->deployments()->createOrUpdate(
    'gpt-5-mini', 'OpenAI', 'gpt-5-mini', '2025-08-07', 'GlobalStandard', 10,
);
$cs->account('my-aif')->regenerateKey('Key1');

// Azure OpenAI inference (Entra or pass API key as 2nd argument)
Azure::instance()->openAi('my-aif')->chat()->create('gpt-5-mini', [
    'messages' => [['role' => 'user', 'content' => 'Hello']],
]);

// Foundry Agent Service
Azure::instance()->foundry('my-aif', 'my-prj')->responses()->create([
    'model' => 'gpt-5-mini',
    'input' => 'Summarize this document',
]);

// Function App ARM — restart, sync triggers, read host keys
$func = Azure::instance()->functionApps($subscriptionId, 'my-rg')->app('my-func');
$func->restart();
$func->syncTriggers();
```

Polling example (app-side — not in the package):

```php
$dep = Azure::instance()->deployments($sub, $rg)->get('tenantflow');
while ($dep->provisioningState && ! $dep->provisioningState->isTerminal()) {
    sleep(5);
    $dep = Azure::instance()->deployments($sub, $rg)->get('tenantflow');
}
```

## API reference

- [Endpoint catalog](ENDPOINTS.md) — human-readable index grouped by Azure service
- [API reference](docs/api-reference.md) — requests, response DTOs, write payloads, and resource gateways
- [Inventory parity](docs/inventory-parity.md) — endpoint coverage vs. Saloon request classes

Regenerate after changing Requests, DTOs, or Resources:

```bash
composer docs:api
composer inventory:parity
```

## Testing

```bash
composer test              # offline unit + core tests (CI)
composer test:coverage     # 100% line coverage on src/ (CI, requires pcov)
composer test:live         # live Azure integration (requires credentials)
composer test:record       # live run with fixture recording enabled
composer inventory:parity  # endpoint coverage report
composer docs:api          # regenerate API reference
composer analyse           # PHPStan level 10
composer format            # Pint
```

CI runs **PHPStan level 10**, **100% unit test coverage** (offline Saloon fixtures), and **live integration tests** when `MICROSOFT_AZURE_*` GitHub secrets are configured.

Set `MICROSOFT_AZURE_TENANT_ID`, `MICROSOFT_AZURE_CLIENT_ID`, `MICROSOFT_AZURE_CLIENT_SECRET`, and `MICROSOFT_AZURE_SUBSCRIPTION_ID` in gitignored `phpunit.xml` (copy from `phpunit.xml.dist` and fill the empty placeholders — never commit real secrets). CI passes the same vars via GitHub Actions secrets.

Integration tests provision their own resource groups via the API and tear them down after each test. Optionally override the Azure region with `MICROSOFT_AZURE_TESTS_LOCATION` (default: `westeurope`).

The service principal needs **Contributor** (or equivalent write/read roles) on `MICROSOFT_AZURE_SUBSCRIPTION_ID` for standard-tier integration tests. Tests skip gracefully with a clear message when OAuth succeeds but RBAC is insufficient.

### Saloon fixtures

Offline tests replay redacted HTTP fixtures from `tests/Fixtures/saloon/`. After a green live run with Contributor access, record or refresh fixtures:

```bash
composer test:record
./vendor/bin/pint
composer test   # verify offline replay still passes
```

Set `MICROSOFT_AZURE_RECORD_FIXTURES=true` (as `test:record` does) to write fixtures during integration tests. Secrets in responses are redacted automatically.

### Live integration tiers

| Tier | Required env | Tests |
|------|----------------|-------|
| Standard | OAuth + subscription ID | Resource group create/get/list/delete; subscription list/get |
| Billing | above + `MICROSOFT_AZURE_TESTS_BILLING_SCOPE` | Subscription alias create/update/list/get; cancel on newly created subscription |

### Billing scope setup

Billing scope is the ARM resource ID of your enrollment account. Alias lifecycle tests skip when `MICROSOFT_AZURE_TESTS_BILLING_SCOPE` is unset.

1. Azure Portal → **Cost Management + Billing** → **Billing accounts**
2. Open your account → **Enrollment accounts** (MCA) or the invoice section path for your agreement type
3. Copy the **Resource ID** — format like `/providers/Microsoft.Billing/billingAccounts/{id}/enrollmentAccounts/{id}`
4. Grant the service principal **Enrollment account subscription creator** (or equivalent billing write role)

```env
MICROSOFT_AZURE_TESTS_BILLING_SCOPE=/providers/Microsoft.Billing/billingAccounts/{billingAccountName}/enrollmentAccounts/{enrollmentAccountName}
```

Teardown cancels the newly created subscription and deletes the alias (best-effort).

## Repository

https://github.com/codebar-ag/laravel-microsoft-azure

## License

MIT
