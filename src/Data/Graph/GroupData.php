<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use Illuminate\Support\Arr;

final class GroupData extends AzureData
{
    public function __construct(
        public string $id,
        public string $displayName,
        public ?string $mailNickname = null,
        public ?string $description = null,
        public ?bool $mailEnabled = null,
        public ?bool $securityEnabled = null,
        /** @var list<string> */
        public array $groupTypes = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: (string) ($data['id'] ?? ''),
            displayName: (string) ($data['displayName'] ?? ''),
            mailNickname: Arr::get($data, 'mailNickname'),
            description: Arr::get($data, 'description'),
            mailEnabled: Arr::get($data, 'mailEnabled'),
            securityEnabled: Arr::get($data, 'securityEnabled'),
            groupTypes: (array) ($data['groupTypes'] ?? []),
        );
    }
}
