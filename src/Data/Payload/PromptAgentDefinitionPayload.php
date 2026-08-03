<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\AgentKind;

/**
 * Definition body for a Foundry `prompt` agent — the lightweight agent kind
 * backed by a model + system instructions + an optional tool list, as
 * opposed to {@see HostedAgentDefinitionPayload}'s container-hosted agents.
 */
final class PromptAgentDefinitionPayload extends AzurePayload
{
    /**
     * @param  list<array<string, mixed>>  $tools
     */
    public function __construct(
        public readonly string $model,
        public readonly string $instructions,
        public readonly array $tools = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'kind' => AgentKind::Prompt->value,
            'model' => $this->model,
            'instructions' => $this->instructions,
        ];

        if ($this->tools !== []) {
            $body['tools'] = $this->tools;
        }

        return $body;
    }
}
