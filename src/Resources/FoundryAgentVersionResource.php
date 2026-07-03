<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class FoundryAgentVersionResource extends FoundryScopedResource
{
    public function container(): FoundryAgentContainerResource
    {
        return new FoundryAgentContainerResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
            $this->agentName,
            $this->agentVersion,
        );
    }
}
