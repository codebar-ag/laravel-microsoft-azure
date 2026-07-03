<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateFineTuningJob extends OpenAiRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly GenericJsonPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/fine_tuning/jobs';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
