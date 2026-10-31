<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use SensitiveParameter;

final class FunctionKeyPayload extends AzurePayload
{
    public function __construct(
        #[SensitiveParameter] public readonly string $value,
    ) {}

    public function toAzureBody(): array
    {
        return ['properties' => ['value' => $this->value]];
    }
}
