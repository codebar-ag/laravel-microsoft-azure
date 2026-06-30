<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateImageGeneration;

final class OpenAiImagesResource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function generate(string $deployment, array $body): array
    {
        $response = $this->dispatchOpenAi(new CreateImageGeneration(
            $deployment,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
