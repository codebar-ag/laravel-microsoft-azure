<?php

namespace CodebarAg\MicrosoftAzure\Requests\LogAnalytics;

use CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsQueryPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class ExecuteWorkspaceQuery extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $workspaceId,
        public readonly LogAnalyticsQueryPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/workspaces/'.$this->workspaceId.'/query';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
