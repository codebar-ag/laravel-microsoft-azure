<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class RegenerateKeyPayload extends AzurePayload
{
    public function __construct(
        public readonly string $keyName,
    ) {}

    public function toAzureBody(): array
    {
        return ['keyName' => $this->keyName];
    }
}
