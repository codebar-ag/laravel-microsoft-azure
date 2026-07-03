<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class SqlFirewallRuleData extends AzureData
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
        $properties = Field::properties($data);

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            startIpAddress: Field::arrString($properties, 'startIpAddress'),
            endIpAddress: Field::arrString($properties, 'endIpAddress'),
        );
    }
}
