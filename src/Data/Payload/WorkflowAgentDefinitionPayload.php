<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\AgentKind;

final class WorkflowAgentDefinitionPayload extends AzurePayload
{
    public function __construct(
        public readonly string $workflow,
        public readonly ?RaiConfigPayload $raiConfig = null,
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'kind' => AgentKind::Workflow->value,
            'workflow' => $this->workflow,
        ];

        if ($this->raiConfig !== null) {
            $body['rai_config'] = $this->raiConfig->toAzureBody();
        }

        return $body;
    }
}
