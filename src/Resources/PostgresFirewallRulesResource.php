<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresFirewallRuleData;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresFirewallRulePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\CreateOrUpdatePostgresFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\DeletePostgresFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresFirewallRule;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresFirewallRules;
use Illuminate\Support\Collection;

/**
 * Firewall rules on one PostgreSQL flexible server. Mirrors
 * {@see SqlFirewallRulesResource}, plus list/get which that resource omits.
 */
final class PostgresFirewallRulesResource extends Resource
{
    /**
     * Start and end both `0.0.0.0` is Azure's sentinel for "allow traffic
     * from Azure-internal services", not a literal address range — the
     * flexible-server equivalent of Azure SQL's AllowAllAzureServices rule.
     */
    public const ALLOW_AZURE_SERVICES_IP = '0.0.0.0';

    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, PostgresFirewallRuleData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListPostgresFirewallRules(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));

        return $this->mapList($response, 'value', fn (array $item) => PostgresFirewallRuleData::fromAzure($item));
    }

    public function get(string $ruleName): PostgresFirewallRuleData
    {
        $response = $this->sendArm(new GetPostgresFirewallRule(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $ruleName,
        ));

        return PostgresFirewallRuleData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(
        string $ruleName,
        string $startIpAddress,
        string $endIpAddress,
    ): PostgresFirewallRuleData {
        $response = $this->sendArm(new CreateOrUpdatePostgresFirewallRule(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $ruleName,
            new PostgresFirewallRulePayload($startIpAddress, $endIpAddress),
        ));

        return PostgresFirewallRuleData::fromAzure($this->jsonArray($response));
    }

    /**
     * Convenience for {@see self::ALLOW_AZURE_SERVICES_IP}.
     */
    public function allowAzureServices(string $ruleName = 'AllowAllAzureServices'): PostgresFirewallRuleData
    {
        return $this->createOrUpdate($ruleName, self::ALLOW_AZURE_SERVICES_IP, self::ALLOW_AZURE_SERVICES_IP);
    }

    public function delete(string $ruleName): void
    {
        $this->sendArm(new DeletePostgresFirewallRule(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $ruleName,
        ));
    }
}
