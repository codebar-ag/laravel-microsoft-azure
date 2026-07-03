<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use Saloon\Enums\Method;

final class ListFiles extends OpenAiRequest
{
    protected Method $method = Method::GET;

    public function __construct(

    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/files';
    }
}
