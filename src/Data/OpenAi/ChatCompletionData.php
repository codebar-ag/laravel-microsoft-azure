<?php

namespace CodebarAg\MicrosoftAzure\Data\OpenAi;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class ChatCompletionUsageData extends AzureData
{
    public function __construct(
        public int $promptTokens = 0,
        public int $completionTokens = 0,
        public int $totalTokens = 0,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        return new self(
            promptTokens: Field::optionalInt($data, 'prompt_tokens', 0),
            completionTokens: Field::optionalInt($data, 'completion_tokens', 0),
            totalTokens: Field::optionalInt($data, 'total_tokens', 0),
        );
    }
}

final class ChatCompletionData extends AzureData
{
    public function __construct(
        public string $id,
        public ?string $model = null,
        /** @var array<int, array<string, mixed>> */
        public array $choices = [],
        public ?ChatCompletionUsageData $usage = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromAzure(array $data): self
    {
        $usage = Field::mixedArray($data, 'usage');
        $choices = [];
        $rawChoices = $data['choices'] ?? [];

        if (is_array($rawChoices)) {
            foreach ($rawChoices as $item) {
                if (is_array($item)) {
                    $choices[] = Field::stringKeyArray($item);
                }
            }
        }

        return new self(
            id: Field::optionalString($data, 'id'),
            model: Field::arrNullableString($data, 'model'),
            choices: $choices,
            usage: $usage !== [] ? ChatCompletionUsageData::fromAzure($usage) : null,
        );
    }
}
