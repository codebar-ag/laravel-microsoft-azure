<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use Illuminate\Support\Arr;

final class FunctionData extends AzureData
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $language = null,
        public ?bool $isDisabled = null,
        public ?string $scriptHref = null,
        public ?string $testData = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            id: Field::optionalString($data, 'id'),
            name: Field::optionalString($data, 'name'),
            language: Field::arrNullableString($data, 'properties.language'),
            isDisabled: is_bool($disabled = Arr::get($data, 'properties.isDisabled')) ? $disabled : null,
            scriptHref: Field::arrNullableString($data, 'properties.scriptHref'),
            testData: Field::arrNullableString($data, 'properties.test_data'),
        );
    }
}
