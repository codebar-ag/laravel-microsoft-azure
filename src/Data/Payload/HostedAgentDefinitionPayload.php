<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\AgentKind;

final class HostedAgentDefinitionPayload extends AzurePayload
{
    /**
     * @param  list<array<string, mixed>>  $containerProtocolVersions
     * @param  array<string, string>  $environmentVariables
     * @param  list<array<string, mixed>>  $tools
     */
    public function __construct(
        public readonly array $containerProtocolVersions,
        public readonly string $cpu,
        public readonly string $memory,
        public readonly ?string $image = null,
        public readonly array $environmentVariables = [],
        public readonly array $tools = [],
        public readonly ?RaiConfigPayload $raiConfig = null,
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'kind' => AgentKind::Hosted->value,
            'container_protocol_versions' => $this->containerProtocolVersions,
            'cpu' => $this->cpu,
            'memory' => $this->memory,
        ];

        if ($this->image !== null) {
            $body['image'] = $this->image;
        }

        if ($this->environmentVariables !== []) {
            $body['environment_variables'] = $this->environmentVariables;
        }

        if ($this->tools !== []) {
            $body['tools'] = $this->tools;
        }

        if ($this->raiConfig !== null) {
            $body['rai_config'] = $this->raiConfig->toAzureBody();
        }

        return $body;
    }
}
