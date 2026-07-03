<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\CreateMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\DeleteMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\GetMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\ListMemoryStores;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\SearchMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\UpdateMemoryStore;
use Illuminate\Support\Collection;

final class FoundryMemoryStoresResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function create(array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new CreateMemoryStore($this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListMemoryStores);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function get(string $memoryStoreId): array
    {
        $response = $this->dispatchFoundry(new GetMemoryStore($memoryStoreId));

        return $this->jsonArray($response);
    }

    /**
     * Extracts memories from a conversation. This is an async operation on
     * Azure's side — the response returns immediately; poll get() until
     * extraction settles (this package does not hide LRO polling behind a
     * helper here, matching its general "orchestration belongs in the
     * consuming app" philosophy).
     *
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function update(string $memoryStoreId, array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new UpdateMemoryStore($memoryStoreId, $this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function search(string $memoryStoreId, array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new SearchMemoryStore($memoryStoreId, $this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    public function delete(string $memoryStoreId): void
    {
        $this->dispatchFoundry(new DeleteMemoryStore($memoryStoreId));
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     */
    private function resolvePayload(array|AzurePayload $body): AzurePayload
    {
        return $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
    }
}
