# Logic Apps & API Management

Two ARM-scoped integration/gateway surfaces. Full REST path tables: [`ENDPOINTS.md` §5](../../ENDPOINTS.md#5-logic-apps-microsoftlogic) and [§4](../../ENDPOINTS.md#4-api-management-subscriptions-microsoftapimanagement).

## Logic Apps

Workflow definitions, run-time triggers, run history, and per-run actions. API version `2019-05-01`.

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$workflows = Azure::instance()->logicWorkflows($subscriptionId, 'my-rg');

$workflows->createOrUpdate(
    workflowName: 'invoice-router',
    location: 'westeurope',
    definition: $workflowDefinitionJson,
    state: 'Enabled',
);

$workflow = $workflows->workflow('invoice-router');
$workflow->enable();
$workflow->triggers()->trigger('manual')->run();
$workflow->runs()->list();
$workflow->runs()->run($runId)->actions()->list();
```

Not covered: `move` (ISE-only — Integration Service Environments are retired), `listSwagger` (designer artifact), workflow-version trigger callback URLs (redundant with the live-trigger callback URL), and run-action `repetitions`/`requestHistories`.

## API Management

Subscription/key lifecycle for an existing APIM instance — product/API/policy configuration is out of scope. API version `2022-08-01`.

```php
$subs = Azure::instance()->apiManagement($subscriptionId, 'my-rg')->service('my-apim')->subscriptions();

$subscription = $subs->create('partner-a', 'Partner A', scope: '/products/partner-tier');
$keys = $subs->subscription('partner-a')->listSecrets(); // primaryKey/secondaryKey only available here
$subs->subscription('partner-a')->regeneratePrimaryKey();
$subs->subscription('partner-a')->revoke(); // PATCH state=suspended, If-Match: *
```

Keys are never returned by a plain `GET`/`PUT` on the subscription — only `listSecrets()` returns them. Not covered: products, policies, APIs, named values, users.
