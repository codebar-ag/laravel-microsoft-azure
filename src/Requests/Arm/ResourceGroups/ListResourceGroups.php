<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListResourceGroups extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/resourcegroups';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_RESOURCES];
    }
}
