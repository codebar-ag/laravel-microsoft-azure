# Observability & cost

Read/observe surfaces: Application Insights, Azure Monitor metrics, Log Analytics KQL queries, Consumption usage, and Cost Management — plus ARM management of Log Analytics workspaces and App Insights components. All ARM-scoped except Log Analytics queries (data plane). Full REST path tables: [`ENDPOINTS.md` §9](../../ENDPOINTS.md#9-log-analytics-kql-query-data-plane) and [§10](../../ENDPOINTS.md#10-resource-provisioning-monitoring--cost-arm).

## Log Analytics — KQL queries

```php
use CodebarAg\MicrosoftAzure\Facades\Azure;

$results = Azure::instance()->logAnalytics()->query(
    workspaceId: $workspaceCustomerId, // the workspace's "Log Analytics customer ID"
    kql: 'AppRequests | where TimeGenerated > ago(1h) | take 50',
);

$results->table()?->rowsAssoc(); // list<array<string, mixed>>
```

Workspace-based Application Insights resources (the default kind since 2019) are queried through this same endpoint using their linked workspace's customer ID — there's no separate Application Insights query surface in this package.

## Log Analytics workspaces & Application Insights (ARM)

```php
Azure::instance()->logAnalyticsWorkspaces($subscriptionId, 'my-rg')->createOrUpdate(
    workspaceName: 'my-workspace',
    location: 'westeurope',
);

Azure::instance()->applicationInsights($subscriptionId, 'my-rg')->createOrUpdate(
    componentName: 'my-app-insights',
    location: 'westeurope',
    workspaceResourceId: $workspaceResourceId, // link to the workspace above
);
```

## Azure Monitor metrics

```php
Azure::instance()->metrics($resourceId)->get(
    metricNames: ['Percentage CPU'],
    timespan: '2026-07-01T00:00:00Z/2026-07-02T00:00:00Z',
    interval: 'PT1H',
    aggregation: 'Average',
);

Azure::instance()->metrics($resourceId)->definitions();
```

## Cost Management & Consumption

```php
Azure::instance()->costManagement($scope)->query(
    from: '2026-06-01',
    to: '2026-06-30',
    grouping: 'ServiceName',
);

Azure::instance()->consumption($scope)->usageDetails($filter);
```

`$scope` is an ARM scope string (subscription, resource group, or billing scope). `usageDetails()` follows `nextLink` pagination internally and returns the full `Collection` — see [Advanced usage → Pagination](../advanced.md#pagination).
