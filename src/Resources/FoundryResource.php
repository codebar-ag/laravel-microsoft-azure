<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class FoundryResource extends FoundryScopedResource
{
    public function agents(): FoundryAgentsResource
    {
        return new FoundryAgentsResource($this->client, $this->accountName, $this->projectName, $this->apiKey);
    }

    public function conversations(): FoundryConversationsResource
    {
        return new FoundryConversationsResource($this->client, $this->accountName, $this->projectName, $this->apiKey);
    }

    public function responses(): FoundryResponsesResource
    {
        return new FoundryResponsesResource($this->client, $this->accountName, $this->projectName, $this->apiKey);
    }

    public function threads(): FoundryThreadsResource
    {
        return new FoundryThreadsResource($this->client, $this->accountName, $this->projectName, $this->apiKey);
    }
}
