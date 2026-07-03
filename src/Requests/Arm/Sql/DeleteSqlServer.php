<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Sql;

use Saloon\Enums\Method;

final class DeleteSqlServer extends SqlRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Sql/servers/'.$this->serverName;
    }
}
