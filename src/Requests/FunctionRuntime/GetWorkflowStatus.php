<?php

namespace CodebarAg\MicrosoftAzure\Requests\FunctionRuntime;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetWorkflowStatus extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $workflowName,
        public readonly string $runId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/workflows/'.$this->workflowName.'/status/'.$this->runId;
    }
}
