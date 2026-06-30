<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListUsers extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly ?string $filter = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/users';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            '$filter' => $this->filter,
        ]);
    }
}
