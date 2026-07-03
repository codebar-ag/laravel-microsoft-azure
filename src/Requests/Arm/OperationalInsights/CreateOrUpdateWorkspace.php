<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights;

use CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsWorkspacePayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateWorkspace extends OperationalInsightsRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $workspaceName,
        public readonly LogAnalyticsWorkspacePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.OperationalInsights/workspaces/'.$this->workspaceName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
