<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\CreateConversationItems;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations\GetConversation;

final class FoundryConversationsResource extends FoundryScopedResource
{
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
    public function createItems(string $conversationId, array $body): array
    {
        $response = $this->dispatchFoundry(new CreateConversationItems(
            $conversationId,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
