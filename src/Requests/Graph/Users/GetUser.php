<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetUser extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $userId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/users/'.$this->userId;
    }
}
