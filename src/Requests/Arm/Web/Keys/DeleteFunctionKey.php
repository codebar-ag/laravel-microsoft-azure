<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys;

use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Enums\Method;

final class DeleteFunctionKey extends WebRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
        public readonly string $functionName,
        public readonly string $keyName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'/functions/'.$this->functionName.'/keys/'.$this->keyName;
    }
}
