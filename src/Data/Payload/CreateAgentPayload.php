<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CreateAgentPayload extends AzurePayload
{
    /**
     * @param  array<string, string>  $metadata
     */
    public function __construct(
        public readonly string $name,
        public readonly AzurePayload $definition,
        public readonly ?string $description = null,
        public readonly array $metadata = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
            'name' => $this->name,
            'definition' => $this->definition->toAzureBody(),
        ];

        if ($this->description !== null) {
            $body['description'] = $this->description;
        }

        if ($this->metadata !== []) {
            $body['metadata'] = $this->metadata;
        }

        return $body;
    }
}
