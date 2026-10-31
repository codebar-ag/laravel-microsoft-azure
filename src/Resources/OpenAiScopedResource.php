<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use Saloon\Http\Request;
use Saloon\Http\Response;
use SensitiveParameter;

abstract class OpenAiScopedResource extends Resource
{
    public function __construct(
        AzureClient $client,
        protected readonly string $accountName,
        #[SensitiveParameter] protected readonly ?string $apiKey = null,
    ) {
        parent::__construct($client);
    }

    protected function dispatchOpenAi(Request $request): Response
    {
        return parent::sendOpenAi($request, $this->accountName, $this->apiKey);
    }
}
