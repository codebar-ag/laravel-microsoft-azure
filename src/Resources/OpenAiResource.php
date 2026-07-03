<?php

namespace CodebarAg\MicrosoftAzure\Resources;

final class OpenAiResource extends OpenAiScopedResource
{
    public function v1(): OpenAiV1Resource
    {
        return new OpenAiV1Resource($this->client, $this->accountName, $this->apiKey);
    }

    public function chat(): OpenAiChatResource
    {
        return new OpenAiChatResource($this->client, $this->accountName, $this->apiKey);
    }

    public function embeddings(): OpenAiEmbeddingsResource
    {
        return new OpenAiEmbeddingsResource($this->client, $this->accountName, $this->apiKey);
    }

    public function responses(): OpenAiResponsesResource
    {
        return new OpenAiResponsesResource($this->client, $this->accountName, $this->apiKey);
    }

    public function models(): OpenAiModelsResource
    {
        return new OpenAiModelsResource($this->client, $this->accountName, $this->apiKey);
    }

    public function audio(): OpenAiAudioResource
    {
        return new OpenAiAudioResource($this->client, $this->accountName, $this->apiKey);
    }

    public function images(): OpenAiImagesResource
    {
        return new OpenAiImagesResource($this->client, $this->accountName, $this->apiKey);
    }

    public function files(): OpenAiFilesResource
    {
        return new OpenAiFilesResource($this->client, $this->accountName, $this->apiKey);
    }

    public function fineTuning(): OpenAiFineTuningResource
    {
        return new OpenAiFineTuningResource($this->client, $this->accountName, $this->apiKey);
    }
}
