<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\CreateEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\DeleteEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\GetEvaluation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\ListEvaluations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations\UpdateEvaluation;
use Illuminate\Support\Collection;

final class FoundryEvaluationsResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function create(array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new CreateEvaluation($this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListEvaluations);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function get(string $evaluationId): array
    {
        $response = $this->dispatchFoundry(new GetEvaluation($evaluationId));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function update(string $evaluationId, array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new UpdateEvaluation($evaluationId, $this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    public function delete(string $evaluationId): void
    {
        $this->dispatchFoundry(new DeleteEvaluation($evaluationId));
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     */
    private function resolvePayload(array|AzurePayload $body): AzurePayload
    {
        return $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
    }
}
