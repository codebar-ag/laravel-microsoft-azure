<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Accounts;

use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\CognitiveServicesRequest;
use Saloon\Enums\Method;

final class DeleteCognitiveServicesAccount extends CognitiveServicesRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.CognitiveServices/accounts/'.$this->accountName;
    }
}
