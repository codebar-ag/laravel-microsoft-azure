<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Applications;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListApplications extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly ?string $filter = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/applications';
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        if ($this->filter === null || $this->filter === '') {
            return [];
        }

        return ['$filter' => $this->filter];
    }
}
