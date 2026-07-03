<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi\V1;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class V1DeleteFile extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $fileId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/v1/files/'.$this->fileId;
    }
}
