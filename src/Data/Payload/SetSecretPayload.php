<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class SetSecretPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public readonly string $value,
        public readonly array $attributes = [],
    ) {}

    public function toAzureBody(): array
    {
        return array_merge(['value' => $this->value], $this->attributes);
    }
}
