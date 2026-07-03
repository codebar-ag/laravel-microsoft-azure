<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Conversations;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class UpdateConversation extends FoundryAgentsRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public readonly string $conversationId,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/conversations/'.$this->conversationId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
