<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class RegenerateStorageKeyPayload extends AzurePayload
{
    public function __construct(
        public readonly string $keyName,
    ) {}

    public function toAzureBody(): array
    {
        return ['keyName' => $this->keyName];
    }
}
