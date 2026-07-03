<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

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
            subscriptionId: Field::optionalString($data, 'subscriptionId'),
        );
    }
}
