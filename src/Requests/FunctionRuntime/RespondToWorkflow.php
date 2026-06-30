<?php

namespace CodebarAg\MicrosoftAzure\Requests\FunctionRuntime;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class RespondToWorkflow extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $workflowName,
        public readonly string $runId,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/api/workflows/'.$this->workflowName.'/respond/'.$this->runId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
