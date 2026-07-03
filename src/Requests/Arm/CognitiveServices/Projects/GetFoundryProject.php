<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects;

use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\CognitiveServicesRequest;
use Saloon\Enums\Method;

final class GetFoundryProject extends CognitiveServicesRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $projectName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.CognitiveServices/accounts/'.$this->accountName
            .'/projects/'.$this->projectName;
    }
}
