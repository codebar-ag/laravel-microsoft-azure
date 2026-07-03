<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class PasswordCredentialData extends AzureData
{
    public function __construct(
        public string $secretText,
        public ?string $keyId = null,
        public ?string $displayName = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            secretText: Field::optionalString($data, 'secretText'),
            keyId: Field::nullableString($data, 'keyId'),
            displayName: Field::nullableString($data, 'displayName'),
        );
    }
}
