<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListConversationItems extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $conversationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/conversations/'.$this->conversationId.'/items';
    }
}
