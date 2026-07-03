<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class DeleteDatasetVersion extends FoundryAgentsRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $datasetName,
        public readonly string $version,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/datasets/'.$this->datasetName.'/versions/'.$this->version;
    }
}
