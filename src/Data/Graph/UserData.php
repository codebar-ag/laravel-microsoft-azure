<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

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
            id: (string) ($data['id'] ?? ''),
            displayName: Arr::get($data, 'displayName'),
            userPrincipalName: Arr::get($data, 'userPrincipalName'),
            mail: Arr::get($data, 'mail'),
            givenName: Arr::get($data, 'givenName'),
            surname: Arr::get($data, 'surname'),
        );
    }
}
