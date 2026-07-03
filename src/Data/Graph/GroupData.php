<?php

namespace CodebarAg\MicrosoftAzure\Data\Graph;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

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
            id: Field::optionalString($data, 'id'),
            displayName: Field::optionalString($data, 'displayName'),
            mailNickname: Field::nullableString($data, 'mailNickname'),
            description: Field::nullableString($data, 'description'),
            mailEnabled: array_key_exists('mailEnabled', $data) && is_bool($data['mailEnabled']) ? $data['mailEnabled'] : null,
            securityEnabled: array_key_exists('securityEnabled', $data) && is_bool($data['securityEnabled']) ? $data['securityEnabled'] : null,
            groupTypes: Field::stringList($data, 'groupTypes'),
        );
    }
}
