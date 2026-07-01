<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class FoundryResource extends FoundryScopedResource
{
    public function agents(): FoundryAgentsResource
    {
        return new FoundryAgentsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function agent(string $agentName): FoundryAgentResource
    {
        return new FoundryAgentResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
            $agentName,
        );
    }

    public function conversations(): FoundryConversationsResource
    {
        return new FoundryConversationsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function responses(): FoundryResponsesResource
    {
        return new FoundryResponsesResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function threads(): FoundryThreadsResource
    {
        return new FoundryThreadsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }
}
