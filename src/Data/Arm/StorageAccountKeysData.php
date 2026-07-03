<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class StorageAccountKeysData extends AzureData
{
    public function __construct(
        /** @var list<array{keyName: ?string, value: ?string}> */
        public array $keys = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $raw = $data['keys'] ?? [];

        /** @var list<array{keyName: ?string, value: ?string}> $keys */
        $keys = [];

        if (is_array($raw)) {
            foreach ($raw as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $key = Field::stringKeyArray($item);

                $keys[] = [
                    'keyName' => Field::arrNullableString($key, 'keyName'),
                    'value' => Field::arrNullableString($key, 'value'),
                ];
            }
        }

        return new self(keys: $keys);
    }
}
