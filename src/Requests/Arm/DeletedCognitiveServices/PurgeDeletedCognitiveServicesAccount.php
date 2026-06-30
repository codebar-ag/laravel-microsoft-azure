<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class PurgeDeletedCognitiveServicesAccount extends Request
{
    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
        public readonly string $accountName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.CognitiveServices/locations/'.$this->location
            .'/deletedAccounts/'.$this->accountName.'/purge';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DELETED_COGNITIVE_SERVICES];
    }
}
