# laravel-microsoft-azure

Thin Azure and Microsoft 365 REST connector for Laravel — Saloon transport only, no business logic.

Covers **ARM**, **Key Vault**, **Microsoft Graph**, and **Kudu** (zip deploy). Orchestration (provisioning sequences, LRO polling, idempotency) belongs in the consuming app.

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
```

Polling example (app-side — not in the package):

```php
$dep = Azure::instance()->deployments($sub, $rg)->get('tenantflow');
while ($dep->provisioningState && ! $dep->provisioningState->isTerminal()) {
    sleep(5);
    $dep = Azure::instance()->deployments($sub, $rg)->get('tenantflow');
}
```

## Testing

```bash
composer test              # offline Saloon fixtures (CI)
composer test:live         # live Azure (requires credentials)
composer inventory:parity  # endpoint coverage report
composer analyse           # PHPStan
composer format            # Pint
```

Set `MICROSOFT_AZURE_TENANT_ID`, `MICROSOFT_AZURE_CLIENT_ID`, `MICROSOFT_AZURE_CLIENT_SECRET`, and `MICROSOFT_AZURE_SUBSCRIPTION_ID` (see `phpunit.xml.dist`). For local integration, copy to `phpunit.xml` and fill credentials only — integration tests provision their own resource groups via the API and tear them down after each test. Optionally override the Azure region with `MICROSOFT_AZURE_TESTS_LOCATION` (default: `westeurope`).

## Repository

https://github.com/codebar-ag/laravel-microsoft-azure

## License

MIT
