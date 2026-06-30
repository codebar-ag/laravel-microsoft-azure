<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Sql;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateSqlFirewallRule extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
        public readonly string $ruleName,
        public readonly string $startIpAddress,
        public readonly string $endIpAddress,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Sql/servers/'.$this->serverName
            .'/firewallRules/'.$this->ruleName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_SQL];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'properties' => [
                'startIpAddress' => $this->startIpAddress,
                'endIpAddress' => $this->endIpAddress,
            ],
        ];
    }
}
