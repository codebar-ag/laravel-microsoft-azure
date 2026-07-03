<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\OpenAi\EmbeddingData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\Embeddings;

final class OpenAiEmbeddingsResource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function create(string $deployment, array $body): EmbeddingData
    {
        $response = $this->dispatchOpenAi(new Embeddings(
            $deployment,
            new GenericJsonPayload($body),
        ));

        return EmbeddingData::fromAzure($this->jsonArray($response));
    }
}
