<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetDatasetVersion extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $datasetName,
        public readonly string $version,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/datasets/'.$this->datasetName.'/versions/'.$this->version;
    }
}
