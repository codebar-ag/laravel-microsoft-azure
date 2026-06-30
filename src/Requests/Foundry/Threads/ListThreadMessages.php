<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Threads;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListThreadMessages extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $threadId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/threads/'.$this->threadId.'/messages';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::FOUNDRY_AGENTS];
    }
}
