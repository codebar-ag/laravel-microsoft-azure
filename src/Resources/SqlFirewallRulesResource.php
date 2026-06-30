<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\SqlFirewallRuleData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\CreateOrUpdateSqlFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Sql\DeleteSqlFirewallRule;

final class SqlFirewallRulesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    public function createOrUpdate(
        string $ruleName,
        string $startIpAddress,
        string $endIpAddress,
    ): SqlFirewallRuleData {
        $response = $this->sendArm(new CreateOrUpdateSqlFirewallRule(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $ruleName,
            $startIpAddress,
            $endIpAddress,
        ));

        return SqlFirewallRuleData::fromAzure($response->json());
    }

    public function delete(string $ruleName): void
    {
        $this->sendArm(new DeleteSqlFirewallRule(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $ruleName,
        ));
    }
}
