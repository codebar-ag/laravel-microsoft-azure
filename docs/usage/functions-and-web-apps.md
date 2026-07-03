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
