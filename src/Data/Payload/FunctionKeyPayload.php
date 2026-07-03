<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class FunctionKeyPayload extends AzurePayload
{
    public function __construct(
        public readonly string $value,
    ) {}

    public function toAzureBody(): array
    {
        return ['properties' => ['value' => $this->value]];
    }
}
