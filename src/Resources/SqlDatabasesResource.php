<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlDatabaseData;
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
}
