<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedVaults;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class PurgeDeletedVault extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
        public readonly string $vaultName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.KeyVault/locations/'.$this->location
            .'/deletedVaults/'.$this->vaultName.'/purge';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DELETED_VAULTS];
    }
}
