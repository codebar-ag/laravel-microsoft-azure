<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions;

use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Enums\Method;

final class GetFunction extends WebRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
        public readonly string $functionName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'/functions/'.$this->functionName;
    }
}
