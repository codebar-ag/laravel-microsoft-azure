<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class OpenAiScopedResource extends Resource
{
    public function __construct(
        AzureClient $client,
        protected readonly string $accountName,
        protected readonly ?string $apiKey = null,
    ) {
        parent::__construct($client);
    }

    protected function dispatchOpenAi(Request $request): Response
    {
        return parent::sendOpenAi($request, $this->accountName, $this->apiKey);
    }
}
