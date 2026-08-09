<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * App Service Plan create/update body.
 *
 * `kind` is nullable and omitted when null rather than defaulted, because a
 * plan's OS is not a formatting choice: ARM's own default is Windows, and a
 * Linux plan needs both `kind: linux` and `properties.reserved: true`. A
 * default here would silently pick one for callers who never thought about it.
 *
 * `properties` is omitted entirely when empty rather than emitted as `[]`.
 * A default Windows `Y1`/`Dynamic` plan is a legitimate request that needs
 * nothing beyond `location` and `sku`, but an empty PHP array JSON-encodes as
 * the array `[]` where ARM's schema wants an object, and ARM rejects the whole
 * body with "The request cannot be interpreted as valid Json: []". Omitting
 * matches {@see StorageAccountPayload} and {@see ResourceGroupPayload}.
 * `CreateOrUpdateStorageTable` deliberately does the opposite and sends
 * `{"properties":{}}` — a table genuinely nests under `properties`, so there
 * the key has to be present and only its encoding was ever wrong.
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
        ];

        if ($this->properties !== []) {
            $body['properties'] = $this->properties;
        }

        if ($this->kind !== null) {
            $body['kind'] = $this->kind;
        }

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
