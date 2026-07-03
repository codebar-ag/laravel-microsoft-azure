<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class SqlDatabasePayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $skuName = 'GP_S_Gen5',
        public readonly string $tier = 'GeneralPurpose',
        public readonly string $family = 'Gen5',
        public readonly ?int $capacity = null,
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'location' => $this->location,
            'sku' => array_filter([
                'name' => $this->skuName,
                'tier' => $this->tier,
                'family' => $this->family,
                'capacity' => $this->capacity,
            ], fn ($value) => $value !== null),
        ];

        if ($this->properties !== []) {
            $body['properties'] = $this->properties;
        }

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
