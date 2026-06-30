<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListDeletedVaults extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.KeyVault/locations/'.$this->location.'/deletedVaults';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DELETED_VAULTS];
    }
}
