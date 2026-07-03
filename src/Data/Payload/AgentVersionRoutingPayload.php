<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class AgentVersionRoutingPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $versionSelector  Body shape per source doc:
     *                                                 {"agent_endpoint":{"version_selector": ...}}. The inner selector shape
     *                                                 (weighted canary vs explicit version pin) is unverified — confirm
     *                                                 against a live tenant before relying on this for production traffic
     *                                                 routing.
     */
    public function __construct(
        public readonly array $versionSelector,
    ) {}

    public function toAzureBody(): array
    {
        return ['agent_endpoint' => ['version_selector' => $this->versionSelector]];
    }
}
