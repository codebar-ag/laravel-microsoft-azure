<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Groups;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListGroups extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly ?string $filter = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/groups';
    }

    protected function defaultQuery(): array
    {
        return array_filter([
            '$filter' => $this->filter,
        ]);
    }
}
