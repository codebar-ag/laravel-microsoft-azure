<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

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
        $properties = (array) ($data['properties'] ?? []);

        return new self(
            id: (string) ($data['id'] ?? ''),
            name: (string) ($data['name'] ?? ''),
            startIpAddress: (string) Arr::get($properties, 'startIpAddress', ''),
            endIpAddress: (string) Arr::get($properties, 'endIpAddress', ''),
        );
    }
}
