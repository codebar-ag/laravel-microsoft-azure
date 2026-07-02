<?php

use CodebarAg\MicrosoftAzure\Resources\ApplicationInsightsResource;
use CodebarAg\MicrosoftAzure\Resources\ConsumptionResource;
use CodebarAg\MicrosoftAzure\Resources\CostManagementResource;
use CodebarAg\MicrosoftAzure\Resources\KeyVaultsResource;
use CodebarAg\MicrosoftAzure\Resources\LogAnalyticsWorkspacesResource;
use CodebarAg\MicrosoftAzure\Resources\LogicWorkflowsResource;
use CodebarAg\MicrosoftAzure\Resources\ManagedIdentitiesResource;
use CodebarAg\MicrosoftAzure\Resources\MetricsResource;
use CodebarAg\MicrosoftAzure\Resources\SqlServersResource;
use CodebarAg\MicrosoftAzure\Resources\StorageAccountsResource;

it('exposes the new ARM resource gateways through the client', function (): void {
    $client = clientWithSeededToken();

    expect($client->storageAccounts('sub-1', 'rg-test'))->toBeInstanceOf(StorageAccountsResource::class)
        ->and($client->vaults('sub-1', 'rg-test'))->toBeInstanceOf(KeyVaultsResource::class)
        ->and($client->managedIdentities('sub-1', 'rg-test'))->toBeInstanceOf(ManagedIdentitiesResource::class)
        ->and($client->sqlServers('sub-1', 'rg-test'))->toBeInstanceOf(SqlServersResource::class)
        ->and($client->logAnalyticsWorkspaces('sub-1', 'rg-test'))->toBeInstanceOf(LogAnalyticsWorkspacesResource::class)
        ->and($client->logicWorkflows('sub-1', 'rg-test'))->toBeInstanceOf(LogicWorkflowsResource::class)
        ->and($client->applicationInsights('sub-1', 'rg-test'))->toBeInstanceOf(ApplicationInsightsResource::class)
        ->and($client->costManagement('subscriptions/sub-1'))->toBeInstanceOf(CostManagementResource::class)
        ->and($client->consumption('subscriptions/sub-1'))->toBeInstanceOf(ConsumptionResource::class)
        ->and($client->metrics('/subscriptions/sub-1/resourceGroups/rg/providers/Microsoft.Sql/servers/s'))
        ->toBeInstanceOf(MetricsResource::class);
});
