<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class AddApplicationPasswordPayload
{
    public function __construct(
        public readonly string $displayName,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toAzureBody(): array
    {
        return [
            'passwordCredential' => [
                'displayName' => $this->displayName,
            ],
        ];
    }
}
