<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Sql;

use CodebarAg\MicrosoftAzure\Data\Payload\SqlDatabasePayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateSqlDatabase extends SqlRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
        public readonly string $databaseName,
        public readonly SqlDatabasePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Sql/servers/'.$this->serverName
            .'/databases/'.$this->databaseName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
