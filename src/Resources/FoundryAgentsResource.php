<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\CreateAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\DeleteAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgent;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgents;
use Illuminate\Support\Collection;

final class FoundryAgentsResource extends FoundryScopedResource
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListAgents);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function create(array $body): array
    {
        $response = $this->dispatchFoundry(new CreateAgent(new GenericJsonPayload($body)));

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
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createVersion(string $agentId, array $body): array
    {
        $response = $this->dispatchFoundry(new CreateAgentVersion($agentId, new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function getVersion(string $agentId, string $version): array
    {
        $response = $this->dispatchFoundry(new GetAgentVersion($agentId, $version));

        return $this->jsonArray($response);
    }
}
