<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices;

use Saloon\Enums\Method;

final class PurgeDeletedCognitiveServicesAccount extends DeletedCognitiveServicesRequest
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
}
