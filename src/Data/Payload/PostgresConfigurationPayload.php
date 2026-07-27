<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * Update body for
 * `Microsoft.DBforPostgreSQL/flexibleServers/configurations`.
 *
 * `source` must be `user-override` for a value to actually be applied —
 * omitting it leaves the parameter reporting the new value while the server
 * keeps using the system default.
 */
final class PostgresConfigurationPayload extends AzurePayload
{
    public function __construct(
        public readonly string $value,
        public readonly string $source = 'user-override',
    ) {}

    public function toAzureBody(): array
    {
        return [
            'properties' => [
                'value' => $this->value,
                'source' => $this->source,
            ],
        ];
    }
}
