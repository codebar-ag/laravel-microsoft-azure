<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects;

use CodebarAg\MicrosoftAzure\Data\Payload\FoundryProjectPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateFoundryProject extends Request implements HasBody
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

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_COGNITIVE_SERVICES];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
