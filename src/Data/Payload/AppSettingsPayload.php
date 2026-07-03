<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class AppSettingsPayload extends AzurePayload
{
    /**
     * @param  array<string, string|null>  $properties
     */
    public function __construct(
        public readonly array $properties,
    ) {}

    public function toAzureBody(): array
    {
        return ['properties' => $this->properties];
    }
}
