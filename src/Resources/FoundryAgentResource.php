<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class FoundryAgentResource extends FoundryScopedResource
{
    public function endpoint(): FoundryAgentEndpointResource
    {
        return new FoundryAgentEndpointResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
            $this->agentName,
        );
    }

    public function version(string $version): FoundryAgentVersionResource
    {
        return new FoundryAgentVersionResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
            $this->agentName,
            $version,
        );
    }
}
