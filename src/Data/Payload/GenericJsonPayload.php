<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class GenericJsonPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function __construct(
        public readonly array $body,
    ) {}

    public function toAzureBody(): array
    {
        return $this->body;
    }
}
