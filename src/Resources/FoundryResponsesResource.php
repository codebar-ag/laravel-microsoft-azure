<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CancelResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\CreateProjectResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\DeleteResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\GetResponse;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Responses\ListResponseInputItems;
use Illuminate\Support\Collection;

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

    /** @return array<string, mixed> */
    public function get(string $responseId): array
    {
        $response = $this->dispatchFoundry(new GetResponse($responseId));

        return $this->jsonArray($response);
    }

    public function delete(string $responseId): void
    {
        $this->dispatchFoundry(new DeleteResponse($responseId));
    }

    /** @return array<string, mixed> */
    public function cancel(string $responseId): array
    {
        $response = $this->dispatchFoundry(new CancelResponse($responseId));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listInputItems(string $responseId): Collection
    {
        $response = $this->dispatchFoundry(new ListResponseInputItems($responseId));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }
}
