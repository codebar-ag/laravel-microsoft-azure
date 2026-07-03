<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ApimSubscriptionKeysData extends AzureData
{
    public function __construct(
        public ?string $primaryKey = null,
        public ?string $secondaryKey = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            primaryKey: Field::nullableString($data, 'primaryKey'),
            secondaryKey: Field::nullableString($data, 'secondaryKey'),
        );
    }
}
