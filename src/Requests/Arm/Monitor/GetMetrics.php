<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Monitor;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetMetrics extends Request
{
    protected Method $method = Method::GET;

    /**
     * @param  list<string>  $metricNames
     */
    public function __construct(
        public readonly string $resourceId,
        public readonly array $metricNames,
        public readonly string $timespan,
        public readonly ?string $interval = null,
        public readonly string $aggregation = 'Total',
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.ltrim($this->resourceId, '/').'/providers/Microsoft.Insights/metrics';
    }

    protected function defaultQuery(): array
    {
        $query = [
            'api-version' => ApiVersion::ARM_MONITOR_METRICS,
            'metricnames' => implode(',', $this->metricNames),
            'timespan' => $this->timespan,
            'aggregation' => $this->aggregation,
        ];

        if ($this->interval !== null) {
            $query['interval'] = $this->interval;
        }

        return $query;
    }
}
