<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices;

use Saloon\Enums\Method;

final class ListDeletedCognitiveServicesAccounts extends DeletedCognitiveServicesRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.CognitiveServices/locations/'.$this->location.'/deletedAccounts';
    }
}
