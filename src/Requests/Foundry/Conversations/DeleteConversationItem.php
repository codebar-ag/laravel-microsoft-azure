<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteConversationItem extends FoundryAgentsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $conversationId,
        public readonly string $itemId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/conversations/'.$this->conversationId.'/items/'.$this->itemId;
    }
}
