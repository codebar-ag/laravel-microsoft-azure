<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListConversations extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/conversations';
    }
}
