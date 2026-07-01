<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CreateServicePrincipalPayload
{
    public function __construct(
        public readonly string $appId,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toAzureBody(): array
    {
        return [
            'appId' => $this->appId,
        ];
    }
}
