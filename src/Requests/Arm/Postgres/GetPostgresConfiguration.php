<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Postgres;

use Saloon\Enums\Method;

final class GetPostgresConfiguration extends PostgresRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
        public readonly string $configurationName,
    ) {}

    public function resolveEndpoint(): string
    {
        return self::serverPath($this->subscriptionId, $this->resourceGroupName, $this->serverName)
            .'/configurations/'.$this->configurationName;
    }
}
