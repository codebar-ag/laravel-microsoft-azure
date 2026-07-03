<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class ApiManagementServicePayload extends AzurePayload
{
    public function __construct(
        public readonly string $location,
        public readonly string $publisherEmail,
        public readonly string $publisherName,
        public readonly string $skuName = 'Consumption',
        public readonly int $skuCapacity = 0,
    ) {}

    public function toAzureBody(): array
    {
        return [
            'location' => $this->location,
            'sku' => ['name' => $this->skuName, 'capacity' => $this->skuCapacity],
            'properties' => [
                'publisherEmail' => $this->publisherEmail,
                'publisherName' => $this->publisherName,
            ],
        ];
    }
}
