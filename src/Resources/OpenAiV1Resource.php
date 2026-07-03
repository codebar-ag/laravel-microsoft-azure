<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\OpenAi\ChatCompletionData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\EmbeddingData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\ModelListData;
use CodebarAg\MicrosoftAzure\Data\OpenAi\OpenAiResponseData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ChatCompletions;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateFineTuningJob;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateImageGeneration;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateResponse;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateSpeech;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1CreateTranscription;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1DeleteFile;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1Embeddings;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ListFiles;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1ListModels;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\V1\V1UploadFile;

/**
 * Azure OpenAI v1 surface (GA, unversioned `/openai/v1/*` paths).
 *
 * Unlike the dated api-version surface, the target model is passed in the
 * request body (`model`) instead of a deployment path segment.
 */
final class OpenAiV1Resource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function chatCompletions(array $body): ChatCompletionData
    {
        $response = $this->dispatchOpenAi(new V1ChatCompletions(new GenericJsonPayload($body)));

        return ChatCompletionData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public function embeddings(array $body): EmbeddingData
    {
        $response = $this->dispatchOpenAi(new V1Embeddings(new GenericJsonPayload($body)));

        return EmbeddingData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $body
     */
    public function responses(array $body): OpenAiResponseData
    {
        $response = $this->dispatchOpenAi(new V1CreateResponse(new GenericJsonPayload($body)));

        return OpenAiResponseData::fromAzure($this->jsonArray($response));
    }

    public function models(): ModelListData
    {
        $response = $this->dispatchOpenAi(new V1ListModels);

        return ModelListData::fromAzure($this->jsonArray($response));
    }

    /** @return array<string, mixed> */
    public function listFiles(): array
    {
        $response = $this->dispatchOpenAi(new V1ListFiles);

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function uploadFile(string $filePath, string $purpose): array
    {
        $response = $this->dispatchOpenAi(new V1UploadFile($filePath, $purpose));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function deleteFile(string $fileId): array
    {
        $response = $this->dispatchOpenAi(new V1DeleteFile($fileId));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function imageGenerations(array $body): array
    {
        $response = $this->dispatchOpenAi(new V1CreateImageGeneration(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function speech(array $body): array
    {
        $response = $this->dispatchOpenAi(new V1CreateSpeech(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function transcriptions(array $body): array
    {
        $response = $this->dispatchOpenAi(new V1CreateTranscription(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createFineTuningJob(array $body): array
    {
        $response = $this->dispatchOpenAi(new V1CreateFineTuningJob(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }
}
