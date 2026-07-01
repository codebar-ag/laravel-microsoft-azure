<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use Saloon\Http\Request;
use Saloon\Http\Response;

/**
 * @phpstan-consistent-constructor
 */
abstract class FoundryScopedResource extends Resource
{
    /**
     * @param  list<FoundryFeature>  $foundryFeatures
     */
    public function __construct(
        AzureClient $client,
        protected readonly string $accountName,
        protected readonly string $projectName,
        protected readonly ?string $apiKey = null,
        protected readonly array $foundryFeatures = [],
        protected readonly ?string $agentName = null,
    ) {
        parent::__construct($client);
    }

    /**
     * @param  list<FoundryFeature>  $features
     */
    public function withFoundryFeatures(array $features): static
    {
        return new static(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $features,
            $this->agentName,
        );
    }

    protected function dispatchFoundry(Request $request): Response
    {
        if ($this->foundryFeatures !== [] && $request instanceof FoundryFeatureRequest) {
            $request = $request->withFoundryFeatures($this->foundryFeatures);
        }

        return parent::sendFoundry($request, $this->accountName, $this->projectName, $this->apiKey);
    }
}
