<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse;

final class FoundryResponsesResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function create(array $body): array
    {
        $response = $this->dispatchFoundry(new CreateProjectResponse(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }
}
