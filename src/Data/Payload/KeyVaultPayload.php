<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class KeyVaultPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $tenantId,
        public readonly string $skuName = 'standard',
        public readonly bool $enableRbacAuthorization = true,
        public readonly ?bool $enablePurgeProtection = null,
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $properties = [
            'tenantId' => $this->tenantId,
            'sku' => ['family' => 'A', 'name' => $this->skuName],
            'enableRbacAuthorization' => $this->enableRbacAuthorization,
        ];

        if ($this->enablePurgeProtection !== null) {
            $properties['enablePurgeProtection'] = $this->enablePurgeProtection;
        }

        $properties += $this->properties;

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
