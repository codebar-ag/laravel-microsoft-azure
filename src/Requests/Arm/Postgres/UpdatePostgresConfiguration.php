<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Postgres;

use CodebarAg\MicrosoftAzure\Data\Payload\PostgresConfigurationPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * PATCH, not PUT — a configuration is a pre-existing server parameter whose
 * value is being overridden, never a resource the caller creates.
 */
final class UpdatePostgresConfiguration extends PostgresRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $serverName,
        public readonly string $configurationName,
        public readonly PostgresConfigurationPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return self::serverPath($this->subscriptionId, $this->resourceGroupName, $this->serverName)
            .'/configurations/'.$this->configurationName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
