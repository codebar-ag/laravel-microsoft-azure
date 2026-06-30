<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Monitor;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListMetricDefinitions extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $resourceId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.ltrim($this->resourceId, '/').'/providers/Microsoft.Insights/metricDefinitions';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_MONITOR_METRICS];
    }
}
