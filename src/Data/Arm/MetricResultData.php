<?php

namespace CodebarAg\MicrosoftAzure\Data\Arm;

use CodebarAg\MicrosoftAzure\Data\AzureData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;

final class MetricResultData extends AzureData
{
    /**
     * @param  list<array{timestamp: ?string, total: mixed}>  $points
     */
    public function __construct(
        public string $name,
        public ?string $unit = null,
        public array $points = [],
    ) {}

    /**
     * Map ONE entry of the metrics `value` array.
     *
     * @param  array<string, mixed>  $item
     */
    public static function fromAzure(array $item): self
    {
        $timeseries = $item['timeseries'] ?? [];
        $firstSeries = is_array($timeseries) && isset($timeseries[0]) && is_array($timeseries[0])
            ? $timeseries[0]
            : [];

        $rawData = $firstSeries['data'] ?? [];

        /** @var list<array{timestamp: ?string, total: mixed}> $points */
        $points = [];

        if (is_array($rawData)) {
            foreach ($rawData as $point) {
                if (! is_array($point)) {
                    continue;
                }

                $value = null;
                foreach (['total', 'average', 'count', 'minimum', 'maximum'] as $aggregation) {
                    if (array_key_exists($aggregation, $point)) {
                        $value = $point[$aggregation];

                        break;
                    }
                }

                $points[] = [
                    'timestamp' => isset($point['timeStamp']) && is_string($point['timeStamp'])
                        ? $point['timeStamp']
                        : (isset($point['timestamp']) && is_string($point['timestamp']) ? $point['timestamp'] : null),
                    'total' => $value,
                ];
            }
        }

        return new self(
            name: Field::arrString($item, 'name.value'),
            unit: Field::arrNullableString($item, 'unit'),
            points: $points,
        );
    }
}
