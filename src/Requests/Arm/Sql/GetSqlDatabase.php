<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Sql;

use Saloon\Enums\Method;

final class GetSqlDatabase extends SqlRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
        public readonly string $databaseName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Sql/servers/'.$this->serverName
            .'/databases/'.$this->databaseName;
    }
}
