<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\OpenAi\OpenAiResponseData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\CreateResponses;

final class OpenAiResponsesResource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function create(array $body): OpenAiResponseData
    {
        $response = $this->dispatchOpenAi(new CreateResponses(
            new GenericJsonPayload($body),
        ));

        return OpenAiResponseData::fromAzure($this->jsonArray($response));
    }
}
