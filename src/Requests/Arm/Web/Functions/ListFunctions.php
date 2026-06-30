<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListFunctions extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'/functions';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_WEB];
    }
}
