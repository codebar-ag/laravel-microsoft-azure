<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceProviders;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class RegisterResourceProvider extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $namespace,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/providers/'.$this->namespace.'/register';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_RESOURCE_PROVIDERS];
    }
}
