# ARM core

Subscriptions, resource groups, template deployments, RBAC, and resource providers — everything against `https://management.azure.com`. Full REST path tables: [`ENDPOINTS.md` §1](../../ENDPOINTS.md#1-arm--core-infrastructure).

## Subscriptions

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

Azure::instance()->subscriptions()->list();
Azure::instance()->subscriptions()->get($subscriptionId);
Azure::instance()->subscriptions()->cancel($subscriptionId);
```

## Creating subscriptions via billing-scope aliases (MCA / EA)

```php
$alias = Azure::instance()->subscriptionAliases()->createOrUpdate(
    aliasName: 'tenant-acme',
    billingScope: '/providers/Microsoft.Billing/billingAccounts/{id}/enrollmentAccounts/{id}',
    displayName: 'Acme Tenant',
);

// Poll until provisioned — wraps awaitProvisioningState() (see Advanced usage)
$alias = Azure::instance()->subscriptionAliases()->await('tenant-acme');
$subscriptionId = $alias->subscriptionId;
```

The service principal needs a billing role like **Enrollment account subscription creator** on the billing scope — see [Troubleshooting](../troubleshooting.md) for the 403 this produces when that role is missing.

## Resource groups

```php
Azure::instance()->resourceGroups($subscriptionId)->list();
Azure::instance()->resourceGroups($subscriptionId)->get('my-rg');
Azure::instance()->resourceGroups($subscriptionId)->createOrUpdate('my-rg', location: 'westeurope');
Azure::instance()->resourceGroups($subscriptionId)->delete('my-rg');
```

## Deployments (ARM templates)

```php
Azure::instance()->deployments($subscriptionId, 'my-rg')->createOrUpdate('tenantflow', $template, $params);

// Poll until terminal, or use the low-level helpers in Advanced usage for custom polling
$dep = Azure::instance()->deployments($subscriptionId, 'my-rg')->await('tenantflow');

// Template outputs (e.g. a generated connection string) are on the terminal DTO
$dep->output('connectionString');
```

## RBAC

```php
Azure::instance()->roleDefinitions($subscriptionId)->findByName('Contributor');
Azure::instance()->roleDefinitions($subscriptionId)->list();

Azure::instance()->roleAssignments($scope)->create(
    roleAssignmentName: (string) \Illuminate\Support\Str::uuid(),
    roleDefinitionId: $roleDefinition->id,
    principalId: $servicePrincipalObjectId,
);
```

`$scope` is any ARM scope string, e.g. `/subscriptions/{sub}` or `/subscriptions/{sub}/resourceGroups/{rg}`.

## Resource providers

```php
Azure::instance()->resourceProviders($subscriptionId)->list();
Azure::instance()->resourceProviders($subscriptionId)->get('Microsoft.CognitiveServices');
Azure::instance()->resourceProviders($subscriptionId)->register('Microsoft.CognitiveServices');
Azure::instance()->resourceProviders($subscriptionId)->awaitRegistered('Microsoft.CognitiveServices');
```

Registering a provider is itself a long-running operation — `awaitRegistered()` polls until the provider's `registrationState` is `Registered`, throwing `LongRunningOperationException` on timeout or an unexpected terminal state.
