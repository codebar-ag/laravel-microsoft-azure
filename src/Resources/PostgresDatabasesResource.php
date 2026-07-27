<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresDatabaseData;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresDatabasePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\CreateOrUpdatePostgresDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\DeletePostgresDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresDatabase;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresDatabases;
use Illuminate\Support\Collection;

/**
 * Databases on one PostgreSQL flexible server.
 *
 * Creating a database over ARM is an alternative to `CREATE DATABASE` over a
 * SQL connection, and is the better option when the caller has ARM
 * credentials but no working SQL login yet. It is also idempotent, where the
 * SQL route has to catch SQLSTATE `42P04` for "database already exists".
 */
final class PostgresDatabasesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, PostgresDatabaseData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListPostgresDatabases(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));

        return $this->mapList($response, 'value', fn (array $item) => PostgresDatabaseData::fromAzure($item));
    }

    public function get(string $databaseName): PostgresDatabaseData
    {
        $response = $this->sendArm(new GetPostgresDatabase(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $databaseName,
        ));

        return PostgresDatabaseData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(
        string $databaseName,
        string $charset = 'UTF8',
        string $collation = 'en_US.utf8',
    ): PostgresDatabaseData {
        $response = $this->sendArm(new CreateOrUpdatePostgresDatabase(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $databaseName,
            new PostgresDatabasePayload($charset, $collation),
        ));

        return PostgresDatabaseData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $databaseName): void
    {
        $this->sendArm(new DeletePostgresDatabase(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $databaseName,
        ));
    }
}
