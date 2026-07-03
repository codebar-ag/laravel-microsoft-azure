<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteIndexVersion extends FoundryAgentsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $indexName,
        public readonly string $version,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/indexes/'.$this->indexName.'/versions/'.$this->version;
    }
}
