<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Applications;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListApplications extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly ?string $filter = null,
        public readonly ?string $select = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/applications';
    }

    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        $query = [];

        if ($this->filter !== null && $this->filter !== '') {
            $query['$filter'] = $this->filter;
        }

        // Graph returns the whole application object otherwise, and a
        // find-or-create only ever reads three fields off it.
        if ($this->select !== null && $this->select !== '') {
            $query['$select'] = $this->select;
        }

        return $query;
    }
}
