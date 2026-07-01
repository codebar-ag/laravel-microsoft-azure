<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateAgentEndpointInvocation extends Request implements FoundryFeatureRequest, HasBody
{
    use HasFoundryFeatures;
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $agentName,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents/'.$this->agentName.'/endpoint/protocols/invocations';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::FOUNDRY_AGENTS];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
