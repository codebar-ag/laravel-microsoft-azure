<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateTranscription extends OpenAiRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $deployment,
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/deployments/'.$this->deployment.'/audio/transcriptions';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
