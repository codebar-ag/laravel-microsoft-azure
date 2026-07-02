<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class LogicWorkflowPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $parameters
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly array $definition,
        public readonly array $parameters = [],
        public readonly ?string $state = null,
        public readonly ?string $integrationAccountId = null,
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $properties = ['definition' => $this->definition];

        if ($this->parameters !== []) {
            $properties['parameters'] = $this->parameters;
        }

        if ($this->state !== null) {
            $properties['state'] = $this->state;
        }

        if ($this->integrationAccountId !== null) {
            $properties['integrationAccount'] = ['id' => $this->integrationAccountId];
        }

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
