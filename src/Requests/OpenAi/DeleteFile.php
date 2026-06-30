<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteFile extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $fileId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/files/'.$this->fileId;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::OPENAI];
    }
}
