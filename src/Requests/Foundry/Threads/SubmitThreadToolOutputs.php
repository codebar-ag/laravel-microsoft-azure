<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Threads;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class SubmitThreadToolOutputs extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $threadId,
        public readonly string $runId,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/threads/'.$this->threadId.'/runs/'.$this->runId.'/submit_tool_outputs';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::FOUNDRY_AGENTS];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
