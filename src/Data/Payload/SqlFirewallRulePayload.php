<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class SqlFirewallRulePayload extends AzurePayload
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
