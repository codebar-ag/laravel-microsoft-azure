<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Arm\Support\PollAsyncOperation;
use CodebarAg\MicrosoftAzure\Resources\Resource;
use Saloon\Http\Response;

/**
 * Reusable polling for Azure long-running operations (LROs).
 *
 * Two strategies:
 *  - {@see awaitProvisioningState()} re-reads a resource DTO until its
 *    `provisioningState` is terminal (Succeeded / Failed / Canceled).
 *  - {@see awaitAsyncOperation()} follows the `Azure-AsyncOperation` / `Location`
 *    header returned by a 201/202 response, honoring `Retry-After`.
 *
 * Mixed into {@see \CodebarAg\MicrosoftAzure\Resources\Resource} subclasses, so
 * `$this->send()`, `$this->client` and `$this->sleepSeconds()` are available.
 */
trait HandlesLongRunningOperations
{
    /**
     * Poll a fetch callback until the returned DTO's provisioningState is terminal.
     *
     * @template TResource of object
     *
     * @param  callable(): TResource  $fetch  re-reads the resource (e.g. fn () => $this->get($name))
     * @param  (callable(TResource): void)|null  $onTick  invoked with each polled resource
     * @return TResource the terminal resource (provisioningState === Succeeded)
     *
     * @throws LongRunningOperationException on Failed/Canceled or timeout
     */
    public function awaitProvisioningState(
        callable $fetch,
        int $timeoutSeconds = 600,
        int $intervalSeconds = 5,
        ?callable $onTick = null,
    ): object {
        $deadline = $this->now() + $timeoutSeconds;

        do {
            $resource = $fetch();

            if ($onTick !== null) {
                $onTick($resource);
            }

            $state = $resource->provisioningState ?? null;

            if ($state instanceof ProvisioningState && $state->isTerminal()) {
                if ($state !== ProvisioningState::Succeeded) {
                    throw new LongRunningOperationException(
                        "Long-running operation finished in non-success state [{$state->value}].",
                        null,
                        $this->client->name(),
                    );
                }

                return $resource;
            }

            $this->sleepSeconds($intervalSeconds);
        } while ($this->now() <= $deadline);

        throw new LongRunningOperationException(
            "Long-running operation did not reach a terminal state within {$timeoutSeconds}s.",
            null,
            $this->client->name(),
        );
    }

    /**
     * Follow the async-operation header of an accepted (201/202) ARM response.
     *
     * @param  (callable(array<string, mixed>): void)|null  $onTick
     * @return array<string, mixed> the terminal operation body
     *
     * @throws LongRunningOperationException on Failed/Canceled or timeout
     */
    public function awaitAsyncOperation(
        Response $accepted,
        int $timeoutSeconds = 600,
        int $defaultIntervalSeconds = 5,
        ?callable $onTick = null,
    ): array {
        $url = $this->headerString($accepted, 'Azure-AsyncOperation')
            ?? $this->headerString($accepted, 'Location');

        // Operation already completed synchronously (200 with no tracking header).
        if ($url === null || $url === '') {
            return Field::fromJson($accepted->json());
        }

        $interval = $this->retryAfterSeconds($accepted, $defaultIntervalSeconds);
        $deadline = $this->now() + $timeoutSeconds;

        do {
            $response = $this->send(new PollAsyncOperation($url), $this->client->arm());
            $body = Field::fromJson($response->json());

            if ($onTick !== null) {
                $onTick($body);
            }

            $status = Field::nullableString($body, 'status');

            if ($status !== null && $this->isTerminalStatus($status)) {
                if (strcasecmp($status, 'Succeeded') !== 0) {
                    throw new LongRunningOperationException(
                        "Async operation finished in non-success state [{$status}].",
                        null,
                        $this->client->name(),
                    );
                }

                return $body;
            }

            $interval = $this->retryAfterSeconds($response, $interval);
            $this->sleepSeconds($interval);
        } while ($this->now() <= $deadline);

        throw new LongRunningOperationException(
            "Async operation did not complete within {$timeoutSeconds}s.",
            null,
            $this->client->name(),
        );
    }

    private function isTerminalStatus(string $status): bool
    {
        return in_array(
            strtolower($status),
            ['succeeded', 'failed', 'canceled', 'cancelled'],
            true,
        );
    }

    private function retryAfterSeconds(Response $response, int $fallback): int
    {
        $header = $this->headerString($response, 'Retry-After');

        return is_numeric($header) ? max(0, (int) $header) : $fallback;
    }

    /**
     * Read a response header as a single string (first value if multi-valued).
     */
    private function headerString(Response $response, string $name): ?string
    {
        $value = $response->header($name);

        if (is_array($value)) {
            $value = $value[0] ?? null;
        }

        return is_string($value) && $value !== '' ? $value : null;
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
