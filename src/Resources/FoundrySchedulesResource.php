<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\CreateOrUpdateSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\DeleteSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\GetSchedule;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\ListSchedules;
use Illuminate\Support\Collection;

final class FoundrySchedulesResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function createOrUpdate(string $scheduleId, array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
        $response = $this->dispatchFoundry(new CreateOrUpdateSchedule($scheduleId, $payload));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListSchedules);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function get(string $scheduleId): array
    {
        $response = $this->dispatchFoundry(new GetSchedule($scheduleId));

        return $this->jsonArray($response);
    }

    public function delete(string $scheduleId): void
    {
        $this->dispatchFoundry(new DeleteSchedule($scheduleId));
    }

    public function runs(string $scheduleId): FoundryScheduleRunsResource
    {
        return new FoundryScheduleRunsResource(
            $this->client,
            $this->accountName,
            $this->projectName,
            $this->apiKey,
            $scheduleId,
        );
    }
}
