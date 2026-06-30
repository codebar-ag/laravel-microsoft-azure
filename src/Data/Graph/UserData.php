<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class UserData extends AzureData
{
    public function __construct(
        public string $id,
        public ?string $displayName = null,
        public ?string $userPrincipalName = null,
        public ?string $mail = null,
        public ?string $givenName = null,
        public ?string $surname = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            displayName: Field::nullableString($data, 'displayName'),
            userPrincipalName: Field::nullableString($data, 'userPrincipalName'),
            mail: Field::nullableString($data, 'mail'),
            givenName: Field::nullableString($data, 'givenName'),
            surname: Field::nullableString($data, 'surname'),
        );
    }
}
