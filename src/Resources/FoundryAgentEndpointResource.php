<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointInvocation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\AgentEndpoints\CreateAgentEndpointResponse;

final class FoundryAgentEndpointResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createResponse(array $body): array
    {
        $response = $this->dispatchFoundry(new CreateAgentEndpointResponse(
            (string) $this->agentName,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createInvocation(array $body): array
    {
        $response = $this->dispatchFoundry(new CreateAgentEndpointInvocation(
            (string) $this->agentName,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
