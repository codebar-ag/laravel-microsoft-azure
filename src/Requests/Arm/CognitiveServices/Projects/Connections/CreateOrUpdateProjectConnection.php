<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Projects\Connections;

use CodebarAg\MicrosoftAzure\Data\Payload\ProjectConnectionPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\CognitiveServicesRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * `PUT .../accounts/{account}/projects/{project}/connections/{connection}` —
 * confirmed against
 * https://learn.microsoft.com/en-us/rest/api/aifoundry/accountmanagement/project-connections/create,
 * a management-plane (ARM) call. Project connections are NOT reachable
 * through the data-plane `{account}.services.ai.azure.com/api/projects/...`
 * host any Foundry*Request in this family otherwise uses — that host 405s on
 * this path.
 */
final class CreateOrUpdateProjectConnection extends CognitiveServicesRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $accountName,
        public readonly string $projectName,
        public readonly string $connectionName,
        public readonly ProjectConnectionPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.CognitiveServices/accounts/'.$this->accountName
            .'/projects/'.$this->projectName
            .'/connections/'.$this->connectionName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
