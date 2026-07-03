<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts;

use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\CognitiveServicesRequest;
use Saloon\Enums\Method;

final class ListCognitiveServicesAccounts extends CognitiveServicesRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/providers/Microsoft.CognitiveServices/accounts';
    }
}
