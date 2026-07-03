<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class SqlServerPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly ?string $administratorLogin = null,
        public readonly string $version = '12.0',
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $properties = ['version' => $this->version]
            + ($this->administratorLogin !== null ? ['administratorLogin' => $this->administratorLogin] : [])
            + $this->properties;

        $body = [
            'location' => $this->location,
            'properties' => $properties,
        ];

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
