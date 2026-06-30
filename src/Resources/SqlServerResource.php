<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlServerData;
use CodebarAg\MicrosoftAzure\Data\Payload\SqlServerPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlServer;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlServer;

final class SqlServerResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    public function get(): SqlServerData
    {
        $response = $this->sendArm(new GetSqlServer(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));

        return SqlServerData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        ?string $administratorLogin = null,
        string $version = '12.0',
        array $properties = [],
        array $tags = [],
    ): SqlServerData {
        $response = $this->sendArm(new CreateOrUpdateSqlServer(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            new SqlServerPayload($location, $administratorLogin, $version, $properties, $tags),
        ));

        return SqlServerData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteSqlServer(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));
    }

    public function databases(): SqlDatabasesResource
    {
        return new SqlDatabasesResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        );
    }

    public function firewallRules(): SqlFirewallRulesResource
    {
        return new SqlFirewallRulesResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        );
    }
}
