<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Payload\SqlDatabasePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\GetSqlDatabase;

final class SqlDatabasesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    public function get(string $databaseName): SqlDatabaseData
    {
        $response = $this->sendArm(new GetSqlDatabase(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $databaseName,
        ));

        return SqlDatabaseData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $database,
        string $location,
        string $skuName = 'GP_S_Gen5',
        string $tier = 'GeneralPurpose',
        string $family = 'Gen5',
        ?int $capacity = null,
        array $properties = [],
        array $tags = [],
    ): SqlDatabaseData {
        $response = $this->sendArm(new CreateOrUpdateSqlDatabase(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $database,
            new SqlDatabasePayload($location, $skuName, $tier, $family, $capacity, $properties, $tags),
        ));

        return SqlDatabaseData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $database): void
    {
        $this->sendArm(new DeleteSqlDatabase(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $database,
        ));
    }
}
