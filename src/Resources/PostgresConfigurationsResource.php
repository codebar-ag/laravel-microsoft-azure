<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\PostgresConfigurationData;
use CodebarAg\MicrosoftAzure\Data\Payload\PostgresConfigurationPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\GetPostgresConfiguration;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\ListPostgresConfigurations;
use CodebarAg\MicrosoftAzure\Requests\Arm\Postgres\UpdatePostgresConfiguration;
use Illuminate\Support\Collection;

/**
 * Server parameters on one PostgreSQL flexible server — the ARM surface for
 * `postgresql.conf`.
 *
 * Two parameters matter for a hardened shared server:
 * `require_secure_transport` (`ON` forces TLS) and `azure.extensions`
 * (the allowlist of extensions `CREATE EXTENSION` may load). Both are
 * static: {@see PostgresConfigurationData::requiresRestart()} reports `true`
 * for them, so a change is not live until the server restarts.
 */
final class PostgresConfigurationsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $server,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, PostgresConfigurationData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListPostgresConfigurations(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
        ));

        return $this->mapList($response, 'value', fn (array $item) => PostgresConfigurationData::fromAzure($item));
    }

    public function get(string $configurationName): PostgresConfigurationData
    {
        $response = $this->sendArm(new GetPostgresConfiguration(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $configurationName,
        ));

        return PostgresConfigurationData::fromAzure($this->jsonArray($response));
    }

    public function update(
        string $configurationName,
        string $value,
        string $source = 'user-override',
    ): PostgresConfigurationData {
        $response = $this->sendArm(new UpdatePostgresConfiguration(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->server,
            $configurationName,
            new PostgresConfigurationPayload($value, $source),
        ));

        return PostgresConfigurationData::fromAzure($this->jsonArray($response));
    }

    /**
     * Convenience for the TLS-enforcement parameter.
     */
    public function requireSecureTransport(bool $enabled = true): PostgresConfigurationData
    {
        return $this->update('require_secure_transport', $enabled ? 'ON' : 'OFF');
    }
}
