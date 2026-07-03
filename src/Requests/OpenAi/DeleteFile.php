<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use Saloon\Enums\Method;

final class DeleteFile extends OpenAiRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $fileId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/files/'.$this->fileId;
    }
}
