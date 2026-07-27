<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Postgres;

use Saloon\Enums\Method;

final class ListPostgresFlexibleServersByResourceGroup extends PostgresRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.DBforPostgreSQL/flexibleServers';
    }
}
