<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\AgentKind;

final class CodeAgentDefinitionPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $definition  Raw code-agent fields per Azure's
     *                                            schema. Unverified — no public field-level schema was available at design
     *                                            time; only the `kind` discriminator is guaranteed, everything else passes
     *                                            through as-is until confirmed against a live tenant.
     */
    public function __construct(
        public readonly array $definition = [],
    ) {}

    public function toAzureBody(): array
    {
        return ['kind' => AgentKind::Code->value] + $this->definition;
    }
}
