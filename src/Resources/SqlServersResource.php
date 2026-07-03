<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlServerData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\ListSqlServersByResourceGroup;
use Illuminate\Support\Collection;

final class SqlServersResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, SqlServerData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListSqlServersByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroup,
        ));

        return $this->mapList($response, 'value', fn (array $item) => SqlServerData::fromAzure($item));
    }

    public function server(string $serverName): SqlServerResource
    {
        return new SqlServerResource(
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
        ?string $administratorLogin = null,
        string $version = '12.0',
        array $properties = [],
        array $tags = [],
    ): SqlServerData {
        return $this->server($serverName)->createOrUpdate($location, $administratorLogin, $version, $properties, $tags);
    }
}
