# Storage

ARM management of storage accounts, keys, and blob containers, plus the Storage Queue data plane (send/receive/delete messages). Full REST path tables: [`ENDPOINTS.md` §10](../../ENDPOINTS.md#10-resource-provisioning-monitoring--cost-arm) and [§12](../../ENDPOINTS.md#12-storage-queue-data-plane).

## Storage accounts (ARM)

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$accounts = Azure::instance()->storageAccounts($subscriptionId, 'my-rg');

$accounts->account('myacct')->get();
$accounts->account('myacct')->createOrUpdate(location: 'westeurope');
$accounts->account('myacct')->delete();

$keys = $accounts->account('myacct')->listKeys();
$accounts->account('myacct')->regenerateKey('key1');
```

## Blob containers

```php
Azure::instance()->storageAccounts($subscriptionId, 'my-rg')
    ->account('myacct')
    ->blobContainers()
    ->createOrUpdate('uploads');

Azure::instance()->storageAccounts($subscriptionId, 'my-rg')
    ->account('myacct')
    ->blobContainers()
    ->setManagementPolicy([/* lifecycle policy rules */]);
```

## Storage Queue data plane

Message bodies are base64-encoded automatically (Azure rejects raw XML-unsafe markup in the message body). API version `2025-05-05`.

```php
// Entra ID by default — needs the "Storage Queue Data Contributor" RBAC role
$queue = Azure::instance()->storageAccounts($subscriptionId, 'my-rg')->account('myacct')->queue('orders');

$queue->sendMessage('hello world', visibilityTimeoutSeconds: 0, messageTtlSeconds: 3600);
$messages = $queue->receiveMessages(numberOfMessages: 5);
$queue->deleteMessage($messages->first()->messageId, $messages->first()->popReceipt);
```

```php
// ...or Shared Key, using an account key from listKeys() instead of Entra ID
$key = Azure::instance()->storageAccounts($subscriptionId, 'my-rg')->account('myacct')->listKeys()->keys[0]['value'];
$queue = Azure::instance()->storageAccounts($subscriptionId, 'my-rg')->account('myacct')->queue('orders', accountKey: $key);
```

**Two distinct auth modes, chosen per call via the optional `$accountKey` argument on `queue()`:**
- Omit it → Entra ID bearer token (`https://storage.azure.com/.default`); the caller's service principal needs the `Storage Queue Data Contributor` RBAC role.
- Pass an account key (from `listKeys()`) → Shared Key (Full) request signing, computed per-request via HMAC-SHA256 over the canonicalized headers and resource.

Scope is intentionally narrow: message send/receive/delete only — no queue management, peek, or update-message operations. See [Troubleshooting](../troubleshooting.md) for Shared Key auth failure modes (stale key, clock skew).
