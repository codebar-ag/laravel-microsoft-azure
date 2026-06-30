<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components;

use CodebarAg\MicrosoftAzure\Data\Payload\ApplicationInsightsComponentPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateComponent extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $componentName,
        public readonly ApplicationInsightsComponentPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Insights/components/'.$this->componentName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_APP_INSIGHTS];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
