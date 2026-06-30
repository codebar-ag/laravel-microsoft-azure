<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateSpeech;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateTranscription;

final class OpenAiAudioResource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function speech(string $deployment, array $body): array
    {
        $response = $this->dispatchOpenAi(new CreateSpeech(
            $deployment,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function transcription(string $deployment, array $body): array
    {
        $response = $this->dispatchOpenAi(new CreateTranscription(
            $deployment,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
