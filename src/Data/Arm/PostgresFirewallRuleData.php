<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

/**
 * A firewall rule on a PostgreSQL flexible server
 * (`Microsoft.DBforPostgreSQL/flexibleServers/firewallRules`).
 *
 * Unlike Azure SQL there is no "allow all Azure services" toggle on the
 * server itself — the equivalent is a rule with start and end both
 * `0.0.0.0`, which Azure interprets as "allow Azure-internal traffic"
 * rather than as a literal address.
 */
final class PostgresFirewallRuleData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public string $startIpAddress,
        public string $endIpAddress,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            startIpAddress: Field::arrString($data, 'properties.startIpAddress'),
            endIpAddress: Field::arrString($data, 'properties.endIpAddress'),
        );
    }
}
