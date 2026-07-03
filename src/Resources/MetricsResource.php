<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\MetricResultData;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\GetMetrics;
use CodebarAg\MicrosoftAzure\Requests\Arm\Monitor\ListMetricDefinitions;
use Illuminate\Support\Collection;

final class MetricsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $resourceId,
    ) {
        parent::__construct($client);
    }

    /**
     * @param  list<string>  $metricNames
     * @return Collection<int, MetricResultData>
     */
    public function get(
        array $metricNames,
        string $timespan,
        ?string $interval = null,
        string $aggregation = 'Total',
    ): Collection {
        $response = $this->sendArm(new GetMetrics(
            $this->resourceId,
            $metricNames,
            $timespan,
            $interval,
            $aggregation,
        ));

        return $this->mapList($response, 'value', fn (array $item) => MetricResultData::fromAzure($item));
    }

    /**
     * @return Collection<int, array{name: string, unit: string|null}>
     */
    public function definitions(): Collection
    {
        $response = $this->sendArm(new ListMetricDefinitions($this->resourceId));

        return $this->mapList($response, 'value', fn (array $item) => [
            'name' => Field::arrString($item, 'name.value'),
            'unit' => Field::arrNullableString($item, 'unit'),
        ]);
    }
}
