<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\CreateAgentPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\UpdateAgentPayload;
use CodebarAg\MicrosoftAzure\Enums\AgentKind;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentVersions;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgent;
use Illuminate\Support\Collection;

final class FoundryAgentsResource extends FoundryScopedResource
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(?AgentKind $kind = null): Collection
    {
        $response = $this->dispatchFoundry(new ListAgents($kind));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /**
     * @param  array<string, mixed>|CreateAgentPayload  $body
     * @return array<string, mixed>
     */
    public function create(array|CreateAgentPayload $body): array
    {
        $payload = $this->resolveAgentPayload($body);
        $response = $this->dispatchFoundry(new CreateAgent($payload));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function get(string $agentId): array
    {
        $response = $this->dispatchFoundry(new GetAgent($agentId));

        return $this->jsonArray($response);
    }

    public function delete(string $agentId): void
    {
        $this->dispatchFoundry(new DeleteAgent($agentId));
    }

    /**
     * @param  array<string, mixed>|UpdateAgentPayload  $body
     * @return array<string, mixed>
     */
    public function update(string $agentName, array|UpdateAgentPayload $body): array
    {
        $payload = $body instanceof UpdateAgentPayload
            ? $body
            : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new UpdateAgent($agentName, $payload));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function createVersion(string $agentId, array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload
            ? $body
            : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateAgentVersion($agentId, $payload));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function getVersion(string $agentId, string $version): array
    {
        $response = $this->dispatchFoundry(new GetAgentVersion($agentId, $version));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listVersions(string $agentName): Collection
    {
        $response = $this->dispatchFoundry(new ListAgentVersions($agentName));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    public function deleteVersion(string $agentName, string $version): void
    {
        $this->dispatchFoundry(new DeleteAgentVersion($agentName, $version));
    }

    /**
     * @param  array<string, mixed>|CreateAgentPayload  $body
     */
    private function resolveAgentPayload(array|CreateAgentPayload $body): AzurePayload
    {
        if ($body instanceof CreateAgentPayload) {
            return $body;
        }

        return new GenericJsonPayload($body);
    }
}
