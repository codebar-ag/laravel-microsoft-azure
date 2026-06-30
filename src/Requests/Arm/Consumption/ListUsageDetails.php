<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Consumption;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListUsageDetails extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $scope,
        public readonly ?string $filter = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.ltrim($this->scope, '/').'/providers/Microsoft.Consumption/usageDetails';
    }

    protected function defaultQuery(): array
    {
        $query = ['api-version' => ApiVersion::ARM_CONSUMPTION];

        if ($this->filter !== null) {
            $query['$filter'] = $this->filter;
        }

        return $query;
    }
}
