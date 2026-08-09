<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Applications;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetApplication extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $applicationId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/applications/'.$this->applicationId;
    }
}
