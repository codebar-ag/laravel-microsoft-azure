<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class UserAssignedIdentityPayload extends AzurePayload
{
    /**
     * @param  array<string, string>  $tags
     */
    public function __construct(
        public readonly string $location,
        public readonly array $tags = [],
    ) {}

    public function toAzureBody(): array
    {
        $body = ['location' => $this->location];

        if ($this->tags !== []) {
            $body['tags'] = $this->tags;
        }

        return $body;
    }
}
