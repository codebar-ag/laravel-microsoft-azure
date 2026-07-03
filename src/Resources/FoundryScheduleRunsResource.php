<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\GetScheduleRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Schedules\ListScheduleRuns;
use Illuminate\Support\Collection;

final class FoundryScheduleRunsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $accountName,
        private readonly string $projectName,
        private readonly ?string $apiKey,
        private readonly string $scheduleId,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->sendFoundry(new ListScheduleRuns($this->scheduleId), $this->accountName, $this->projectName, $this->apiKey);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function get(string $runId): array
    {
        $response = $this->sendFoundry(new GetScheduleRun($this->scheduleId, $runId), $this->accountName, $this->projectName, $this->apiKey);

        return $this->jsonArray($response);
    }
}
