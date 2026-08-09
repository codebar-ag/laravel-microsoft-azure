# Functions & Web Apps

Azure Functions ARM lifecycle, App Service, Kudu (SCM) zip-deploy, the Function Runtime HTTP surface, and user-assigned managed identities. Full REST path tables: [`ENDPOINTS.md` §3](../../ENDPOINTS.md#3-azure-functions-arm-microsoftweb) and [§8](../../ENDPOINTS.md#8-function-runtime--agent-framework-workflows).

## Function Apps (ARM, `Microsoft.Web`, api-version `2024-11-01`)

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$app = Azure::instance()->functionApps($subscriptionId, 'my-rg')->app('my-func');

$app->get();
$app->restart();
$app->settings()->update(['FUNCTIONS_WORKER_RUNTIME' => 'dotnet-isolated']);
$app->hostKeys()->list();
$app->functions('FlowRunner')->keys()->list();
$app->syncTriggers();
```

### System-assigned identity

Pass `identityType` to emit the top-level ARM `identity` block. The returned `WebSiteData`
exposes `identityType`, `identityPrincipalId` and `identityTenantId` — the principal id is what
you grant data-plane RBAC (storage, Key Vault) to.

```php
$site = $app->createOrUpdate(
    location: 'switzerlandnorth',
    properties: ['serverFarmId' => $planId],
    identityType: 'SystemAssigned',
);

Azure::instance()
    ->roleAssignments($storageAccountId)
    ->create($roleAssignmentName, $roleDefinitionId, (string) $site->identityPrincipalId);
```

## App Service Plans (`Microsoft.Web/serverfarms`)

A site needs a plan, and a plan is not editable in place: tier and region are fixed at create
time, and plan creation is quota-limited per subscription. So `observe()` first — it answers
`Absent` instead of throwing — and only create when the plan is genuinely missing.

```php
$plans = Azure::instance()->serverFarms($subscriptionId, 'my-rg');

$observation = $plans->plan('my-plan')->observe();

if (! $observation->state->isPresent()) {
    $plans->plan('my-plan')->createOrUpdate(
        location: 'switzerlandnorth',
        skuName: 'Y1',
        skuTier: 'Dynamic',
        kind: 'linux',
        properties: ['reserved' => true],
    );
}
```

`kind` is omitted when null rather than defaulted: ARM's own default is Windows, and a Linux
plan needs both `kind: linux` and `properties.reserved: true`.

## Basic publishing credentials (SCM / FTP)

Basic-auth publish credentials are enabled by default. An app reached only through its managed
identity has no use for them; each channel is its own policy resource.

```php
$app->updateBasicPublishingCredentialsPolicy('scm', false);
$app->updateBasicPublishingCredentialsPolicy('ftp', false);
```

## Kudu (SCM) zip deploy

```php
Azure::instance()->appService('my-func')->zipDeploy('/path/to/intake.zip');
Azure::instance()->appService('my-func')->deploymentStatus($deploymentId);
```

## Function Runtime (durable functions / Agent Framework workflows)

HTTP endpoints auto-generated when a Function App uses `ConfigureDurableWorkflows` (the MAF durable extension). Auth is via `x-functions-key`/host key or an Entra token scoped to the app host.

```php
$runtime = Azure::instance()->functionRuntime('my-func', hostKey: $key);

$runtime->agents()->run('MyDurableAgent', ['input' => $payload]);
$runtime->workflows()->run('FlowRunner', ['input' => $payload]);
$runtime->workflows()->status('FlowRunner', $runId);
$runtime->workflows()->respond('FlowRunner', $runId, ['approved' => true]);
```

## Managed identities

User-assigned managed identities (ARM), typically assigned to a Function App or App Service so it can authenticate to other Azure resources without a stored secret.

```php
Azure::instance()->managedIdentities($subscriptionId, 'my-rg')->list();

Azure::instance()->managedIdentities($subscriptionId, 'my-rg')->createOrUpdate(
    identityName: 'my-func-identity',
    location: 'westeurope',
);

Azure::instance()->managedIdentities($subscriptionId, 'my-rg')->identity('my-func-identity')->get();
```

## Actual-state reads

```php
Azure::instance()->functionApps($subscriptionId, 'my-rg')->app('my-func')->observe()->state;
```

`Available` means the ARM site resource is provisioned, not that the app is running —
`Microsoft.Web` keeps run state on `properties.state` (`Running`/`Stopped`), which stays on
the attached `WebSiteData`. A stopped app is provisioned and available; starting it is a
different repair from creating it. See [Advanced usage](../advanced.md#actual-state-reads-observe).
