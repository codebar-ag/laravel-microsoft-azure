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

    public function toolboxes(): FoundryToolboxesResource
    {
        return new FoundryToolboxesResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function connections(): FoundryConnectionsResource
    {
        return new FoundryConnectionsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function skills(): FoundrySkillsResource
    {
        return new FoundrySkillsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function memoryStores(): FoundryMemoryStoresResource
    {
        return new FoundryMemoryStoresResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function evaluations(): FoundryEvaluationsResource
    {
        return new FoundryEvaluationsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function schedules(): FoundrySchedulesResource
    {
        return new FoundrySchedulesResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function datasets(): FoundryDatasetsResource
    {
        return new FoundryDatasetsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function indexes(): FoundryIndexesResource
    {
        return new FoundryIndexesResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }

    public function redteams(): FoundryRedteamsResource
    {
        return new FoundryRedteamsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $this->foundryFeatures,
        );
    }
}
