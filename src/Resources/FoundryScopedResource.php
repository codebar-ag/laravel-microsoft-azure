<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class FoundryScopedResource extends Resource
{
    public function __construct(
        AzureClient $client,
        protected readonly string $accountName,
        protected readonly string $projectName,
        protected readonly ?string $apiKey = null,
    ) {
        parent::__construct($client);
    }

    protected function dispatchFoundry(Request $request): Response
    {
        return parent::sendFoundry($request, $this->accountName, $this->projectName, $this->apiKey);
    }
}
