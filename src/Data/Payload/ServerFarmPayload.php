<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * App Service Plan create/update body.
 *
 * `kind` is nullable and omitted when null rather than defaulted, because a
 * plan's OS is not a formatting choice: ARM's own default is Windows, and a
 * Linux plan needs both `kind: linux` and `properties.reserved: true`. A
 * default here would silently pick one for callers who never thought about it.
 */
final class ServerFarmPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $skuName = 'Y1',
        public readonly string $skuTier = 'Dynamic',
        public readonly ?string $kind = null,
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'location' => $this->location,
            'sku' => ['name' => $this->skuName, 'tier' => $this->skuTier],
            'properties' => $this->properties,
        ];

        if ($this->kind !== null) {
            $body['kind'] = $this->kind;
        }

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
