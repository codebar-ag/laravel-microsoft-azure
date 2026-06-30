<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class FoundryProjectPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public readonly string $location,
        public readonly array $properties = [],
    ) {}

    public function toAzureBody(): array
    {
        return [
            'location' => $this->location,
            'properties' => $this->properties,
        ];
    }
}
