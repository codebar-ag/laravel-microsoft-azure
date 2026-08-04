<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections;

use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\CognitiveServicesRequest;
use Saloon\Enums\Method;

final class GetProjectConnection extends CognitiveServicesRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $projectName,
        public readonly string $connectionName,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.CognitiveServices/accounts/'.$this->accountName
            .'/projects/'.$this->projectName
            .'/connections/'.$this->connectionName;
    }
}
