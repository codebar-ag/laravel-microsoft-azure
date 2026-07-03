<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class FoundryProjectPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public readonly string $location,
        public readonly array $properties = [],
        public readonly ?string $identityType = null,
    ) {}

    public function toAzureBody(): array
    {
        $body = ['location' => $this->location];

        if ($this->properties !== []) {
            $body['properties'] = $this->properties;
        }

        if ($this->identityType !== null) {
            $body['identity'] = ['type' => $this->identityType];
        }

        return $body;
    }
}
