<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadMessage;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\CreateThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThread;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\GetThreadRun;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\ListThreadMessages;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Threads\SubmitThreadToolOutputs;
use Illuminate\Support\Collection;

final class FoundryThreadsResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function create(array $body): array
    {
        $response = $this->dispatchFoundry(new CreateThread(new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function get(string $threadId): array
    {
        $response = $this->dispatchFoundry(new GetThread($threadId));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createMessage(string $threadId, array $body): array
    {
        $response = $this->dispatchFoundry(new CreateThreadMessage($threadId, new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listMessages(string $threadId): Collection
    {
        $response = $this->dispatchFoundry(new ListThreadMessages($threadId));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function createRun(string $threadId, array $body): array
    {
        $response = $this->dispatchFoundry(new CreateThreadRun($threadId, new GenericJsonPayload($body)));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function getRun(string $threadId, string $runId): array
    {
        $response = $this->dispatchFoundry(new GetThreadRun($threadId, $runId));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    public function submitToolOutputs(string $threadId, string $runId, array $body): array
    {
        $response = $this->dispatchFoundry(new SubmitThreadToolOutputs(
            $threadId,
            $runId,
            new GenericJsonPayload($body),
        ));

        return $this->jsonArray($response);
    }
}
