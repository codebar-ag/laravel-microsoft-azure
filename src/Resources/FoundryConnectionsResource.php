<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\CreateConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\DeleteConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\GetConnection;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Connections\ListConnections;
use Illuminate\Support\Collection;

final class FoundryConnectionsResource extends FoundryScopedResource
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListConnections);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function create(array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateConnection($payload));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function get(string $connectionId): array
    {
        $response = $this->dispatchFoundry(new GetConnection($connectionId));

        return $this->jsonArray($response);
    }

    public function delete(string $connectionId): void
    {
        $this->dispatchFoundry(new DeleteConnection($connectionId));
    }
}
