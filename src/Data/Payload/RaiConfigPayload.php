<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class RaiConfigPayload extends AzurePayload
{
    public function __construct(
        public readonly string $raiPolicyName,
    ) {}

    public function toAzureBody(): array
    {
        return [
            'rai_policy_name' => $this->raiPolicyName,
        ];
    }
}
