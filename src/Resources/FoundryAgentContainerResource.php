<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\GetAgentContainerOperation;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\ListAgentContainerOperations;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Agents\UpdateAgentContainer;
use Illuminate\Support\Collection;

class FoundryAgentContainerResource extends FoundryScopedResource
{
    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function update(array|AzurePayload $body): array
    {
        $payload = $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);

        $response = $this->dispatchFoundry(new UpdateAgentContainer(
            (string) $this->agentName,
            (string) $this->agentVersion,
            $payload,
        ));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listOperations(): Collection
    {
        $response = $this->dispatchFoundry(new ListAgentContainerOperations(
            (string) $this->agentName,
            (string) $this->agentVersion,
        ));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function getOperation(string $operationId): array
    {
        $response = $this->dispatchFoundry(new GetAgentContainerOperation(
            (string) $this->agentName,
            (string) $this->agentVersion,
            $operationId,
        ));

        return $this->jsonArray($response);
    }

    /**
     * Poll getOperation() until its `status` field reaches a terminal value.
     *
     * UNVERIFIED SHAPE: assumes the operation resource has a top-level `status`
     * string with values queued|running|succeeded|failed (per the source doc's
     * one-line "Long-Running-Operation" note, not a confirmed live response
     * sample). Smoke-test against a real hosted-agent container update before
     * treating this as stable — the field name/values may need to change.
     *
     * @param  (callable(array<string, mixed>): void)|null  $onTick
     * @return array<string, mixed>
     *
     * @throws LongRunningOperationException on failed/canceled or timeout
     */
    public function awaitContainerOperation(
        string $operationId,
        int $timeoutSeconds = 600,
        int $intervalSeconds = 5,
        ?callable $onTick = null,
    ): array {
        $deadline = $this->now() + $timeoutSeconds;

        do {
            $operation = $this->getOperation($operationId);

            if ($onTick !== null) {
                $onTick($operation);
            }

            $rawStatus = $operation['status'] ?? '';
            $status = strtolower(is_string($rawStatus) ? $rawStatus : '');

            if (in_array($status, ['succeeded', 'failed', 'canceled', 'cancelled'], true)) {
                if ($status !== 'succeeded') {
                    throw new LongRunningOperationException(
                        "Container operation finished in non-success state [{$status}].",
                        null,
                        $this->client->name(),
                    );
                }

                return $operation;
            }

            $this->sleepSeconds($intervalSeconds);
        } while ($this->now() <= $deadline);

        throw new LongRunningOperationException(
            "Container operation did not reach a terminal state within {$timeoutSeconds}s.",
            null,
            $this->client->name(),
        );
    }

    /**
     * Seam for tests: current unix time.
     */
    protected function now(): int
    {
        return time();
    }

    /**
     * Seam for tests: sleep (no-op when interval <= 0).
     */
    protected function sleepSeconds(int $seconds): void
    {
        if ($seconds > 0) {
            sleep($seconds);
        }
    }
}
