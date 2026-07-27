<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * Create body for `Microsoft.DBforPostgreSQL/flexibleServers/databases`.
 *
 * Defaults match what `CREATE DATABASE` produces on an Azure flexible
 * server, so the ARM route and the SQL route yield the same database.
 */
final class PostgresDatabasePayload extends AzurePayload
{
    public function __construct(
        public readonly string $charset = 'UTF8',
        public readonly string $collation = 'en_US.utf8',
    ) {}

    public function toAzureBody(): array
    {
        return [
            'properties' => [
                'charset' => $this->charset,
                'collation' => $this->collation,
            ],
        ];
    }
}
