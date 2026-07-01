<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Resources\ApplicationInsightsResource;
use CodebarAg\MicrosoftAzure\Resources\AppServiceResource;
use CodebarAg\MicrosoftAzure\Resources\CognitiveServicesResource;
use CodebarAg\MicrosoftAzure\Resources\ConsumptionResource;
use CodebarAg\MicrosoftAzure\Resources\CostManagementResource;
use CodebarAg\MicrosoftAzure\Resources\DeletedCognitiveServicesResource;
use CodebarAg\MicrosoftAzure\Resources\DeletedVaultsResource;
use CodebarAg\MicrosoftAzure\Resources\DeploymentsResource;
use CodebarAg\MicrosoftAzure\Resources\FoundryResource;
use CodebarAg\MicrosoftAzure\Resources\FunctionAppsResource;
use CodebarAg\MicrosoftAzure\Resources\FunctionRuntimeResource;
use CodebarAg\MicrosoftAzure\Resources\GraphResource;
use CodebarAg\MicrosoftAzure\Resources\KeyVaultsResource;
use CodebarAg\MicrosoftAzure\Resources\LogAnalyticsWorkspacesResource;
use CodebarAg\MicrosoftAzure\Resources\ManagedIdentitiesResource;
use CodebarAg\MicrosoftAzure\Resources\MetricsResource;
use CodebarAg\MicrosoftAzure\Resources\OpenAiResource;
use CodebarAg\MicrosoftAzure\Resources\ResourceGroupsResource;
use CodebarAg\MicrosoftAzure\Resources\ResourceProvidersResource;
use CodebarAg\MicrosoftAzure\Resources\RoleAssignmentsResource;
use CodebarAg\MicrosoftAzure\Resources\RoleDefinitionsResource;
use CodebarAg\MicrosoftAzure\Resources\SecretsResource;
use CodebarAg\MicrosoftAzure\Resources\SqlDatabasesResource;
use CodebarAg\MicrosoftAzure\Resources\SqlFirewallRulesResource;
use CodebarAg\MicrosoftAzure\Resources\SqlServersResource;
use CodebarAg\MicrosoftAzure\Resources\StorageAccountsResource;
use CodebarAg\MicrosoftAzure\Resources\SubscriptionAliasesResource;
use CodebarAg\MicrosoftAzure\Resources\SubscriptionsResource;
use CodebarAg\MicrosoftAzure\Resources\VaultResource;

trait InteractsWithResources
{
    abstract protected function resourceClient(): AzureClient;

    public function resourceGroups(string $subscriptionId): ResourceGroupsResource
    {
        return new ResourceGroupsResource($this->resourceClient(), $subscriptionId);
    }

    public function deployments(string $subscriptionId, string $resourceGroup): DeploymentsResource
    {
        return new DeploymentsResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function roleAssignments(string $scope): RoleAssignmentsResource
    {
        return new RoleAssignmentsResource($this->resourceClient(), $scope);
    }

    public function roleDefinitions(string $subscriptionId): RoleDefinitionsResource
    {
        return new RoleDefinitionsResource($this->resourceClient(), $subscriptionId);
    }

    public function resourceProviders(string $subscriptionId): ResourceProvidersResource
    {
        return new ResourceProvidersResource($this->resourceClient(), $subscriptionId);
    }

    public function deletedVaults(string $subscriptionId): DeletedVaultsResource
    {
        return new DeletedVaultsResource($this->resourceClient(), $subscriptionId);
    }

    public function deletedCognitiveServices(string $subscriptionId): DeletedCognitiveServicesResource
    {
        return new DeletedCognitiveServicesResource($this->resourceClient(), $subscriptionId);
    }

    public function sql(string $subscriptionId, string $resourceGroup, string $server): SqlFirewallRulesResource
    {
        return new SqlFirewallRulesResource($this->resourceClient(), $subscriptionId, $resourceGroup, $server);
    }

    public function sqlDatabases(string $subscriptionId, string $resourceGroup, string $server): SqlDatabasesResource
    {
        return new SqlDatabasesResource($this->resourceClient(), $subscriptionId, $resourceGroup, $server);
    }

    public function vault(string $vaultName): VaultResource
    {
        return new VaultResource($this->resourceClient(), $vaultName);
    }

    public function secrets(string $vaultName): SecretsResource
    {
        return new SecretsResource($this->resourceClient(), $vaultName);
    }

    public function graph(): GraphResource
    {
        return new GraphResource($this->resourceClient());
    }

    public function appService(string $appName): AppServiceResource
    {
        return new AppServiceResource($this->resourceClient(), $appName);
    }

    public function subscriptions(): SubscriptionsResource
    {
        return new SubscriptionsResource($this->resourceClient());
    }

    public function subscriptionAliases(): SubscriptionAliasesResource
    {
        return new SubscriptionAliasesResource($this->resourceClient());
    }

    public function cognitiveServices(string $subscriptionId, string $resourceGroup): CognitiveServicesResource
    {
        return new CognitiveServicesResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function functionApps(string $subscriptionId, string $resourceGroup): FunctionAppsResource
    {
        return new FunctionAppsResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function openAi(string $accountName, ?string $apiKey = null): OpenAiResource
    {
        return new OpenAiResource($this->resourceClient(), $accountName, $apiKey);
    }

    public function foundry(string $accountName, string $projectName, ?string $apiKey = null): FoundryResource
    {
        return new FoundryResource($this->resourceClient(), $accountName, $projectName, $apiKey);
    }

    public function functionRuntime(string $appName, ?string $hostKey = null): FunctionRuntimeResource
    {
        return new FunctionRuntimeResource($this->resourceClient(), $appName, $hostKey);
    }

    public function storageAccounts(string $subscriptionId, string $resourceGroup): StorageAccountsResource
    {
        return new StorageAccountsResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function vaults(string $subscriptionId, string $resourceGroup): KeyVaultsResource
    {
        return new KeyVaultsResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function managedIdentities(string $subscriptionId, string $resourceGroup): ManagedIdentitiesResource
    {
        return new ManagedIdentitiesResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function sqlServers(string $subscriptionId, string $resourceGroup): SqlServersResource
    {
        return new SqlServersResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function logAnalyticsWorkspaces(string $subscriptionId, string $resourceGroup): LogAnalyticsWorkspacesResource
    {
        return new LogAnalyticsWorkspacesResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function applicationInsights(string $subscriptionId, string $resourceGroup): ApplicationInsightsResource
    {
        return new ApplicationInsightsResource($this->resourceClient(), $subscriptionId, $resourceGroup);
    }

    public function costManagement(string $scope): CostManagementResource
    {
        return new CostManagementResource($this->resourceClient(), $scope);
    }

    public function consumption(string $scope): ConsumptionResource
    {
        return new ConsumptionResource($this->resourceClient(), $scope);
    }

    public function metrics(string $resourceId): MetricsResource
    {
        return new MetricsResource($this->resourceClient(), $resourceId);
    }
}
