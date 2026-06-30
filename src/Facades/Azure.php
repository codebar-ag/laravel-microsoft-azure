<?php

namespace CodebarAg\MicrosoftAzure\Facades;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Config\ConnectionConfig;
use CodebarAg\MicrosoftAzure\MicrosoftAzureManager;
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
use CodebarAg\MicrosoftAzure\Resources\RoleAssignmentsResource;
use CodebarAg\MicrosoftAzure\Resources\SecretsResource;
use CodebarAg\MicrosoftAzure\Resources\SqlDatabasesResource;
use CodebarAg\MicrosoftAzure\Resources\SqlFirewallRulesResource;
use CodebarAg\MicrosoftAzure\Resources\SqlServersResource;
use CodebarAg\MicrosoftAzure\Resources\StorageAccountsResource;
use CodebarAg\MicrosoftAzure\Resources\SubscriptionAliasesResource;
use CodebarAg\MicrosoftAzure\Resources\SubscriptionsResource;
use CodebarAg\MicrosoftAzure\Resources\VaultResource;
use Illuminate\Support\Facades\Facade;

/**
 * @see MicrosoftAzureManager
 *
 * @method static AzureClient instance(string|ConnectionConfig|null $connection = null)
 * @method static AzureClient connection(ConnectionConfig $config)
 * @method static ResourceGroupsResource resourceGroups(string $subscriptionId)
 * @method static DeploymentsResource deployments(string $subscriptionId, string $resourceGroup)
 * @method static RoleAssignmentsResource roleAssignments(string $scope)
 * @method static DeletedVaultsResource deletedVaults(string $subscriptionId)
 * @method static DeletedCognitiveServicesResource deletedCognitiveServices(string $subscriptionId)
 * @method static SqlFirewallRulesResource sql(string $subscriptionId, string $resourceGroup, string $server)
 * @method static SqlDatabasesResource sqlDatabases(string $subscriptionId, string $resourceGroup, string $server)
 * @method static SqlServersResource sqlServers(string $subscriptionId, string $resourceGroup)
 * @method static VaultResource vault(string $vaultName)
 * @method static SecretsResource secrets(string $vaultName)
 * @method static KeyVaultsResource vaults(string $subscriptionId, string $resourceGroup)
 * @method static StorageAccountsResource storageAccounts(string $subscriptionId, string $resourceGroup)
 * @method static ManagedIdentitiesResource managedIdentities(string $subscriptionId, string $resourceGroup)
 * @method static LogAnalyticsWorkspacesResource logAnalyticsWorkspaces(string $subscriptionId, string $resourceGroup)
 * @method static ApplicationInsightsResource applicationInsights(string $subscriptionId, string $resourceGroup)
 * @method static CostManagementResource costManagement(string $scope)
 * @method static ConsumptionResource consumption(string $scope)
 * @method static MetricsResource metrics(string $resourceId)
 * @method static GraphResource graph()
 * @method static AppServiceResource appService(string $appName)
 * @method static CognitiveServicesResource cognitiveServices(string $subscriptionId, string $resourceGroup)
 * @method static FunctionAppsResource functionApps(string $subscriptionId, string $resourceGroup)
 * @method static OpenAiResource openAi(string $accountName, ?string $apiKey = null)
 * @method static FoundryResource foundry(string $accountName, string $projectName, ?string $apiKey = null)
 * @method static FunctionRuntimeResource functionRuntime(string $appName, ?string $hostKey = null)
 * @method static SubscriptionsResource subscriptions()
 * @method static SubscriptionAliasesResource subscriptionAliases()
 */
class Azure extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MicrosoftAzureManager::class;
    }
}
