<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders;

use Saloon\Enums\Method;

final class ListResourceProviders extends ResourceProvidersRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/providers';
    }
}
