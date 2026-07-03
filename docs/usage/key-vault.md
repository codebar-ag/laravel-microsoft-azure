# Key Vault

Covers both the ARM control plane (vault resource lifecycle) and the data plane (secrets), plus soft-delete purge. Full REST path tables: [`ENDPOINTS.md` §1](../../ENDPOINTS.md#1-arm--core-infrastructure) (deleted-vault purge) and [§11](../../ENDPOINTS.md#11-key-vault-graph-kudu).

## Data plane — secrets

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

Azure::instance()->vault('my-kv')->secrets()->set('webhook-token', $token);
Azure::instance()->vault('my-kv')->secrets()->get('webhook-token');
Azure::instance()->vault('my-kv')->secrets()->list();
Azure::instance()->vault('my-kv')->secrets()->versions('webhook-token');
Azure::instance()->vault('my-kv')->secrets()->delete('webhook-token');
```

`vault($name)` resolves the vault host (`{name}.vault.azure.net`) and authenticates with a `key_vault`-audience token — separate from the ARM token used below.

## Control plane — vault resource management (ARM)

```php
Azure::instance()->vaults($subscriptionId, 'my-rg')->list();

Azure::instance()->vaults($subscriptionId, 'my-rg')->createOrUpdate(
    vaultName: 'my-kv',
    location: 'westeurope',
    tenantId: $tenantId,
    enableRbacAuthorization: true,
);

Azure::instance()->vaults($subscriptionId, 'my-rg')->vault('my-kv')->get();
```

## Soft-deleted vaults

Key Vault soft-deletes by default; a purged vault name can't be reused until the deleted vault is purged (or its retention period elapses).

```php
Azure::instance()->deletedVaults($subscriptionId)->list(location: 'westeurope');
Azure::instance()->deletedVaults($subscriptionId)->purge(location: 'westeurope', vaultName: 'my-kv');
```

The same pattern exists for soft-deleted Cognitive Services accounts — see [Foundry & Azure OpenAI](foundry-and-openai.md).
