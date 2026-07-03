<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ApimSubscriptionState;

final class ApimSubscriptionData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $displayName = null,
        public ?string $scope = null,
        public ?ApimSubscriptionState $state = null,
        public ?string $ownerId = null,
        public ?string $primaryKey = null,
        public ?string $secondaryKey = null,
        public ?string $createdDate = null,
        public ?string $startDate = null,
        public ?string $expirationDate = null,
        public ?string $endDate = null,
        public ?string $notificationDate = null,
        public ?string $stateComment = null,
        public bool $allowTracing = false,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = Field::properties($data);
        $state = Field::nullableString($properties, 'state');

        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            displayName: Field::nullableString($properties, 'displayName'),
            scope: Field::nullableString($properties, 'scope'),
            state: $state !== null ? ApimSubscriptionState::tryFrom($state) : null,
            ownerId: Field::nullableString($properties, 'ownerId'),
            primaryKey: Field::nullableString($properties, 'primaryKey'),
            secondaryKey: Field::nullableString($properties, 'secondaryKey'),
            createdDate: Field::nullableString($properties, 'createdDate'),
            startDate: Field::nullableString($properties, 'startDate'),
            expirationDate: Field::nullableString($properties, 'expirationDate'),
            endDate: Field::nullableString($properties, 'endDate'),
            notificationDate: Field::nullableString($properties, 'notificationDate'),
            stateComment: Field::nullableString($properties, 'stateComment'),
            allowTracing: Field::bool($properties, 'allowTracing'),
        );
    }
}
