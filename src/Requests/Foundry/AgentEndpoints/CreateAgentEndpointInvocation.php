<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class CreateAgentEndpointInvocation extends FoundryAgentsRequest implements FoundryFeatureRequest, HasBody
{
    use HasFoundryFeatures;
    use SendsJsonObjectBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $agentName,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents/'.$this->agentName.'/endpoint/protocols/invocations';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
