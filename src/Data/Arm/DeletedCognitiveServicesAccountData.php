<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class DeletedCognitiveServicesAccountData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $location = null,
        public ?string $resourceGroup = null,
        public ?string $deletionDate = null,
        public ?string $scheduledPurgeDate = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $properties = Field::properties($data);
        $id = Field::optionalString($data, 'id');

        return new self(
            id: $id,
            name: Field::optionalString($data, 'name'),
            location: Field::nullableString($data, 'location'),
            resourceGroup: self::resourceGroupFromId($id),
            deletionDate: Field::nullableString($properties, 'deletionDate'),
            scheduledPurgeDate: Field::nullableString($properties, 'scheduledPurgeDate'),
        );
    }

    /**
     * The resource group segment of a deleted account's id.
     *
     * ARM returns no `resourceGroup` field on these, and the purge route needs
     * one — so the id is the only place it can come from. The group itself is
     * typically already deleted by the time an account is listed here; the
     * deleted account outlives it and stays addressable under its name.
     *
     * Ids look like:
     * `/subscriptions/{sub}/providers/Microsoft.CognitiveServices/locations/{loc}/resourceGroups/{rg}/deletedAccounts/{name}`
     */
    private static function resourceGroupFromId(string $id): ?string
    {
        $segments = explode('/', trim($id, '/'));

        foreach ($segments as $index => $segment) {
            if (strcasecmp($segment, 'resourceGroups') === 0 && isset($segments[$index + 1])) {
                return $segments[$index + 1];
            }
        }

        return null;
    }
}
