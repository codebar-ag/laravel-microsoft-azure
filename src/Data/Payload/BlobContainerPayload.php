<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class BlobContainerPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public readonly string $publicAccess = 'None',
        public readonly array $properties = [],
    ) {}

    public function toAzureBody(): array
    {
        return [
            'properties' => ['publicAccess' => $this->publicAccess] + $this->properties,
        ];
    }
}
