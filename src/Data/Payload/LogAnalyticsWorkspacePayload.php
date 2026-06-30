<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class LogAnalyticsWorkspacePayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $skuName = 'PerGB2018',
        public readonly int $retentionInDays = 30,
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'location' => $this->location,
            'properties' => [
                'sku' => ['name' => $this->skuName],
                'retentionInDays' => $this->retentionInDays,
            ] + $this->properties,
        ];

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
