<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Resources\AppServiceResource;
use CodebarAg\MicrosoftAzure\Resources\DeletedCognitiveServicesResource;
use CodebarAg\MicrosoftAzure\Resources\DeletedVaultsResource;
use CodebarAg\MicrosoftAzure\Resources\DeploymentsResource;
use CodebarAg\MicrosoftAzure\Resources\GraphResource;
use CodebarAg\MicrosoftAzure\Resources\ResourceGroupsResource;
use CodebarAg\MicrosoftAzure\Resources\RoleAssignmentsResource;
use CodebarAg\MicrosoftAzure\Resources\SecretsResource;
use CodebarAg\MicrosoftAzure\Resources\SqlDatabasesResource;
use CodebarAg\MicrosoftAzure\Resources\SqlFirewallRulesResource;
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
}
