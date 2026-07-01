<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ResourceProviderData extends AzureData
{
    public function __construct(
        public string $namespace,
        public string $registrationState,
        public ?string $id = null,
    ) {}

    public function isRegistered(): bool
    {
        return strcasecmp($this->registrationState, 'Registered') === 0;
    }

    public function isRegistering(): bool
    {
        return strcasecmp($this->registrationState, 'Registering') === 0;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            namespace: Field::optionalString($data, 'namespace'),
            registrationState: Field::optionalString($data, 'registrationState'),
            id: Field::nullableString($data, 'id'),
        );
    }
}
