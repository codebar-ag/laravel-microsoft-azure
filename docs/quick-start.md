# Quick start

Assumes you've already [installed the package](installation.md), [configured](configuration.md) `MICROSOFT_AZURE_TENANT_ID`/`CLIENT_ID`/`CLIENT_SECRET`/`SUBSCRIPTION_ID`, and have an app registration with at least `Reader` access on the subscription.

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

// List resource groups in your subscription
$groups = Azure::instance()->resourceGroups(config('laravel-microsoft-azure.connections.default.subscription_id'))->list();

foreach ($groups as $group) {
    echo $group->name.PHP_EOL;
}

// Read a secret from an existing Key Vault
$secret = Azure::instance()->vault('my-keyvault')->secrets()->get('my-secret');

echo $secret->value;
```

That's it — one facade (`Azure`), one method call to resolve a connection (`Azure::instance()`), then a chain of fluent gateway methods per Azure service. Every gateway method returns a typed DTO (or a `Collection` of them), not a raw array.

## Where to go next

- [Usage](usage.md) — the full catalog of surfaces this package covers, one page per group.
- [Configuration](configuration.md) — every config key, including multi-tenant connections.
- [Advanced usage](advanced.md) — long-running-operation polling, retries, rate limiting.
- [Troubleshooting](troubleshooting.md) — what to do when a call throws.
