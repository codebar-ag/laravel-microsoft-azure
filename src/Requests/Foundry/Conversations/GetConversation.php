<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetConversation extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $conversationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/conversations/'.$this->conversationId;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::FOUNDRY_AGENTS];
    }
}
