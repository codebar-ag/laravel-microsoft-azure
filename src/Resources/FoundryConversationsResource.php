<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CompactConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\DeleteConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\DeleteConversationItem;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversationItem;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\ListConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\ListConversations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\UpdateConversation;
use Illuminate\Support\Collection;

final class FoundryConversationsResource extends FoundryScopedResource
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListConversations);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function create(array $body): array
    {
        $response = $this->dispatchFoundry(new CreateConversation(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function get(string $conversationId): array
    {
        $response = $this->dispatchFoundry(new GetConversation($conversationId));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function update(string $conversationId, array $body): array
    {
        $response = $this->dispatchFoundry(new UpdateConversation($conversationId, new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    public function delete(string $conversationId): void
    {
        $this->dispatchFoundry(new DeleteConversation($conversationId));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createItems(string $conversationId, array $body): array
    {
        $response = $this->dispatchFoundry(new CreateConversationItems(
            $conversationId,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listItems(string $conversationId): Collection
    {
        $response = $this->dispatchFoundry(new ListConversationItems($conversationId));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function getItem(string $conversationId, string $itemId): array
    {
        $response = $this->dispatchFoundry(new GetConversationItem($conversationId, $itemId));

        return $this->jsonArray($response);
    }

    public function deleteItem(string $conversationId, string $itemId): void
    {
        $this->dispatchFoundry(new DeleteConversationItem($conversationId, $itemId));
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function compact(string $conversationId, array $options = []): array
    {
        $response = $this->dispatchFoundry(new CompactConversation($conversationId, new GenericJsonPayload($options)));

        return $this->jsonArray($response);
    }
}
