<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Postgres;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Http\Request;

/**
 * @internal Shared base for this resource family's requests; not part of the public API.
 */
abstract class PostgresRequest extends Request
{
    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_POSTGRESQL->value()];
    }

    /**
     * Every request in this family is scoped to one flexible server, so the
     * shared prefix is built once here rather than repeated per request.
     */
    protected static function serverPath(
        string $subscriptionId,
        string $resourceGroupName,
        string $serverName,
    ): string {
        return '/subscriptions/'.$subscriptionId
            .'/resourceGroups/'.$resourceGroupName
            .'/providers/Microsoft.DBforPostgreSQL/flexibleServers/'.$serverName;
    }
}
