<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\OpenAi\ChatCompletionData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\OpenAi\ChatCompletions;

final class OpenAiChatResource extends OpenAiScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     */
    public function completions(string $deployment, array $body): ChatCompletionData
    {
        $response = $this->dispatchOpenAi(new ChatCompletions(
            $deployment,
            new GenericJsonPayload($body),
        ));

        return ChatCompletionData::fromAzure($this->jsonArray($response));
    }
}
