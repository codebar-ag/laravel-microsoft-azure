<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresFlexibleServerData;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresFlexibleServerPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\CreateOrUpdatePostgresFlexibleServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\DeletePostgresFlexibleServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresFlexibleServer;

/**
 * A single PostgreSQL flexible server, and the entry point to its
 * databases, firewall rules, and server parameters.
 */
final class PostgresFlexibleServerResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    public function get(): PostgresFlexibleServerData
    {
        $response = $this->sendArm(new GetPostgresFlexibleServer(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));

        return PostgresFlexibleServerData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        string $skuName,
        string $skuTier,
        ?string $administratorLogin = null,
        ?string $administratorLoginPassword = null,
        ?string $version = null,
        ?int $storageSizeGB = null,
        array $properties = [],
        array $tags = [],
    ): PostgresFlexibleServerData {
        $response = $this->sendArm(new CreateOrUpdatePostgresFlexibleServer(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            new PostgresFlexibleServerPayload(
                location: $location,
                skuName: $skuName,
                skuTier: $skuTier,
                administratorLogin: $administratorLogin,
                administratorLoginPassword: $administratorLoginPassword,
                version: $version,
                storageSizeGB: $storageSizeGB,
                properties: $properties,
                tags: $tags,
            ),
        ));

        return PostgresFlexibleServerData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeletePostgresFlexibleServer(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));
    }

    public function databases(): PostgresDatabasesResource
    {
        return new PostgresDatabasesResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        );
    }

    public function firewallRules(): PostgresFirewallRulesResource
    {
        return new PostgresFirewallRulesResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        );
    }

    public function configurations(): PostgresConfigurationsResource
    {
        return new PostgresConfigurationsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        );
    }
}
