<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ServicePrincipalData extends AzureData
{
    public function __construct(
        public string $id,
        public string $appId,
        public ?string $displayName = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            appId: Field::optionalString($data, 'appId'),
            displayName: Field::nullableString($data, 'displayName'),
        );
    }
}
