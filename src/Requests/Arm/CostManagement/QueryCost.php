<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement;

use CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class QueryCost extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $scope,
        public readonly CostQueryPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.ltrim($this->scope, '/').'/providers/Microsoft.CostManagement/query';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_COST_MANAGEMENT];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
