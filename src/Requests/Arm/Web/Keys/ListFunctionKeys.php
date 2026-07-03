<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys;

use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Enums\Method;

final class ListFunctionKeys extends WebRequest
{
    protected Method $method = Method::POST;

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
            .'/functions/'.$this->functionName.'/listkeys';
    }
}
