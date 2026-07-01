<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class UpdateAgentPayload extends AzurePayload
{
    /**
     * @param  array<string, string>  $metadata
     */
    public function __construct(
        public readonly AzurePayload $definition,
        public readonly ?string $description = null,
        public readonly array $metadata = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = [
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
