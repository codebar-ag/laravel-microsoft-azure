<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Subscriptions;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListSubscriptions extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/subscriptions';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_SUBSCRIPTIONS];
    }
}
