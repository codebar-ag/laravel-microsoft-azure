<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites;

use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Enums\Method;

final class StartSite extends WebRequest
{
    protected Method $method = Method::POST;

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
            .'/start';
    }
}
