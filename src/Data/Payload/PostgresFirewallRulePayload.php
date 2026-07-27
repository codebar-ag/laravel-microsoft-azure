<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * Create/update body for
 * `Microsoft.DBforPostgreSQL/flexibleServers/firewallRules`.
 */
final class PostgresFirewallRulePayload extends AzurePayload
{
    public function __construct(
        public readonly string $startIpAddress,
        public readonly string $endIpAddress,
    ) {}

    public function toAzureBody(): array
    {
        return [
            'properties' => [
                'startIpAddress' => $this->startIpAddress,
                'endIpAddress' => $this->endIpAddress,
            ],
        ];
    }
}
