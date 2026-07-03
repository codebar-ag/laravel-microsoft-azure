<?php

namespace CodebarAg\MicrosoftAzure\Data\OpenAi;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class EmbeddingData extends AzureData
{
    public function __construct(
        public string $model,
        /** @var array<int, array<string, mixed>> */
        public array $data = [],
        public int $promptTokens = 0,
        public int $totalTokens = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $usage = Field::mixedArray($data, 'usage');
        $embeddings = [];
        $raw = $data['data'] ?? [];

        if (is_array($raw)) {
            foreach ($raw as $item) {
                if (is_array($item)) {
                    $embeddings[] = Field::stringKeyArray($item);
                }
            }
        }

        return new self(
            model: Field::optionalString($data, 'model'),
            data: $embeddings,
            promptTokens: Field::optionalInt($usage, 'prompt_tokens', 0),
            totalTokens: Field::optionalInt($usage, 'total_tokens', 0),
        );
    }
}
