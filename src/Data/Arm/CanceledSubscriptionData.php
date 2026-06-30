<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;

final class CanceledSubscriptionData extends AzureData
{
    public function __construct(
        public string $subscriptionId,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            subscriptionId: (string) ($data['subscriptionId'] ?? ''),
        );
    }
}
