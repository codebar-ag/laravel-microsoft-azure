<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class FunctionRuntimeScopedResource extends Resource
{
    public function __construct(
        AzureClient $client,
        protected readonly string $appName,
        protected readonly ?string $hostKey = null,
    ) {
        parent::__construct($client);
    }

    protected function dispatchFunctionRuntime(Request $request): Response
    {
        return parent::sendFunctionRuntime($request, $this->appName, $this->hostKey);
    }
}
