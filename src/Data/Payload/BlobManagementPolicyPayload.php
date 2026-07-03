<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class BlobManagementPolicyPayload extends AzurePayload
{
    /**
     * @param  array<int, mixed>  $rules
     */
    public function __construct(
        public readonly array $rules,
    ) {}

    public function toAzureBody(): array
    {
        return [
            'properties' => [
                'policy' => [
                    'rules' => $this->rules,
                ],
            ],
        ];
    }
}
