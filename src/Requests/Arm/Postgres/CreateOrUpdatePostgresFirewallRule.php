<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Postgres;

use CodebarAg\MicrosoftAzure\Data\Payload\PostgresFirewallRulePayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdatePostgresFirewallRule extends PostgresRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
        public readonly string $firewallRuleName,
        public readonly PostgresFirewallRulePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return self::serverPath($this->subscriptionId, $this->resourceGroupName, $this->serverName)
            .'/firewallRules/'.$this->firewallRuleName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
