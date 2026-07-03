<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects;

use CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\CognitiveServicesRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateFoundryProject extends CognitiveServicesRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $projectName,
        public readonly FoundryProjectPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.CognitiveServices/accounts/'.$this->accountName
            .'/projects/'.$this->projectName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
