<?php

namespace CodebarAg\MicrosoftAzure\Data\OpenAi;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ModelListData extends AzureData
{
    /**
     * @param  array<int, array<string, mixed>>  $data
     */
    public function __construct(
        public array $data = [],
    ) {}

    /**
     * @param  array<string, mixed>  $response
     */
    public static function fromAzure(array $response): self
    {
        $items = $response['data'] ?? [];
        $data = [];

        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_array($item)) {
                    $data[] = Field::stringKeyArray($item);
                }
            }
        }

        return new self(
            data: $data,
        );
    }
}
