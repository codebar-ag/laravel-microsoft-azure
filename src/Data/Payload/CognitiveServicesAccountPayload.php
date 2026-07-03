<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CognitiveServicesAccountPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $kind = 'AIServices',
        public readonly string $skuName = 'S0',
        public readonly array $properties = [],
        public readonly array $tags = [],
        public readonly ?string $identityType = null,
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'location' => $this->location,
            'kind' => $this->kind,
            'sku' => ['name' => $this->skuName],
        ];

        if ($this->properties !== []) {
            $body['properties'] = $this->properties;
        }

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        if ($this->identityType !== null) {
            $body['identity'] = ['type' => $this->identityType];
        }

        return $body;
    }
}
