<?php

namespace CodebarAg\MicrosoftAzure\Requests\Kudu;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetDeploymentStatus extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $deploymentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/deployments/'.$this->deploymentId;
    }
}
