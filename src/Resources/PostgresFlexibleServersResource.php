<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresFlexibleServerData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresFlexibleServersByResourceGroup;
use Illuminate\Support\Collection;

/**
 * Resource-group-scoped gateway for Azure Database for PostgreSQL
 * **flexible** servers. Mirrors {@see SqlServersResource}.
 *
 * Single Server (`Microsoft.DBforPostgreSQL/servers`) is retired and
 * deliberately not covered.
 */
final class PostgresFlexibleServersResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, PostgresFlexibleServerData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListPostgresFlexibleServersByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroup,
        ));

        return $this->mapList($response, 'value', fn (array $item) => PostgresFlexibleServerData::fromAzure($item));
    }

    public function server(string $serverName): PostgresFlexibleServerResource
    {
        return new PostgresFlexibleServerResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $serverName,
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $serverName,
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
        return $this->server($serverName)->createOrUpdate(
            $location,
            $skuName,
            $skuTier,
            $administratorLogin,
            $administratorLoginPassword,
            $version,
            $storageSizeGB,
            $properties,
            $tags,
        );
    }
}
