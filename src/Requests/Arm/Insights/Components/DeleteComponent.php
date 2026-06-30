<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteComponent extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $componentName,
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
}
