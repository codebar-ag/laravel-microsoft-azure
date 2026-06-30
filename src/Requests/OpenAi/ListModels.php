<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListModels extends Request
{
    protected Method $method = Method::GET;

    public function __construct(

    ) {}

    public function resolveEndpoint(): string
    {
        return '/openai/models';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::OPENAI];
    }
}
