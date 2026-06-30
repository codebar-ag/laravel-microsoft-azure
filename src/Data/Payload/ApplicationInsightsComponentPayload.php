<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class ApplicationInsightsComponentPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly string $applicationType = 'web',
        public readonly string $kind = 'web',
        public readonly ?string $workspaceResourceId = null,
        public readonly array $properties = [],
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $properties = ['Application_Type' => $this->applicationType]
            + ($this->workspaceResourceId !== null ? ['WorkspaceResourceId' => $this->workspaceResourceId] : [])
            + $this->properties;

        $body = [
            'location' => $this->location,
            'kind' => $this->kind,
            'properties' => $properties,
        ];

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
