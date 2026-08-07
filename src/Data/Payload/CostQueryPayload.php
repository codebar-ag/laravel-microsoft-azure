<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

use CodebarAg\MicrosoftAzure\Enums\CostGranularity;

/**
 * The body of a Cost Management `query` call.
 *
 * `$grouping` accepts either a single dimension name or a list of them.
 * Cost Management's own `dataset.grouping` is always an array, so a list is the
 * shape that matches the API; the bare string is kept because it was this
 * class's original signature and is the common case.
 *
 * **Granularity changes the response shape**, not just its resolution: at
 * {@see CostGranularity::Daily} every row gains a `UsageDate` column and the
 * row count multiplies by the days in the period. It therefore defaults to
 * {@see CostGranularity::None}, so an existing caller keeps getting exactly
 * what it got before.
 */
final class CostQueryPayload extends AzurePayload
{
    /**
     * @param  string|list<string>  $grouping  one dimension, or several
     */
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly string|array $grouping = 'ServiceName',
        public readonly CostGranularity $granularity = CostGranularity::None,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toAzureBody(): array
    {
        $dataset = [
            'granularity' => $this->granularity->value,
            'aggregation' => [
                'totalCost' => [
                    'name' => 'Cost',
                    'function' => 'Sum',
                ],
            ],
        ];

        $groupings = $this->groupings();

        if ($groupings !== []) {
            $dataset['grouping'] = $groupings;
        }

        return [
            'type' => 'ActualCost',
            'timeframe' => 'Custom',
            'timePeriod' => [
                'from' => $this->from,
                'to' => $this->to,
            ],
            'dataset' => $dataset,
        ];
    }

    /**
     * Every grouping dimension, in the array-of-objects shape ARM expects.
     *
     * An empty list is dropped rather than sent as `[]`: Cost Management
     * rejects an empty `grouping` array, while omitting the key entirely is
     * valid and means "no grouping at all" — which is the only way to ask for a
     * single total.
     *
     * @return list<array{type: string, name: string}>
     */
    private function groupings(): array
    {
        $names = is_string($this->grouping) ? [$this->grouping] : $this->grouping;

        return array_values(array_map(
            fn (string $name): array => ['type' => 'Dimension', 'name' => $name],
            array_filter($names, fn (string $name): bool => $name !== ''),
        ));
    }
}
