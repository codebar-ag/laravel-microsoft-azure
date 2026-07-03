<?php

namespace CodebarAg\MicrosoftAzure\Requests\OpenAi\V1;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class V1ListModels extends Request
{
    protected Method $method = Method::GET;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return '/openai/v1/models';
    }
}
