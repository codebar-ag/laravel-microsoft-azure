<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateFineTuningJob;

final class OpenAiFineTuningResource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createJob(array $body): array
    {
        $response = $this->dispatchOpenAi(new CreateFineTuningJob(
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
