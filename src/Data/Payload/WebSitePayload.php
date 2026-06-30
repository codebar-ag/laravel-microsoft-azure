<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class WebSitePayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $kind = 'functionapp',
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'location' => $this->location,
            'kind' => $this->kind,
            'properties' => $this->properties,
        ];

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
