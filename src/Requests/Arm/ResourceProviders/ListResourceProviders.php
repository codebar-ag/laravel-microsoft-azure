<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListResourceProviders extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/providers';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_RESOURCE_PROVIDERS];
    }
}
