<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Support\Field;
use CodebarAg\MicrosoftAzure\Requests\Arm\Support\GetNextPage;
use CodebarAg\MicrosoftAzure\Transport\ResponseValidator;
use Illuminate\Support\Collection;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;

abstract class Resource
{
    public function __construct(
        protected readonly AzureClient $client,
    ) {}

    protected function send(Request $request, Connector $connector): Response
    {
        $response = $connector->send($request);

        ResponseValidator::validate($response, $this->client->name());

        return $response;
    }

    protected function sendArm(Request $request): Response
    {
        return $this->send($request, $this->client->arm());
    }

    protected function sendKeyVault(Request $request, string $vaultHost): Response
    {
        return $this->send($request, $this->client->keyVault($vaultHost));
    }

    protected function sendGraph(Request $request): Response
    {
        return $this->send($request, $this->client->graph());
    }

    protected function sendKudu(Request $request, string $appName): Response
    {
        return $this->send($request, $this->client->kudu($appName));
    }

    protected function sendOpenAi(Request $request, string $accountName, ?string $apiKey = null): Response
    {
        return $this->send($request, $this->client->openAiConnector($accountName, $apiKey));
    }

    protected function sendFoundry(
        Request $request,
        string $accountName,
        string $projectName,
        ?string $apiKey = null,
    ): Response {
        return $this->send($request, $this->client->foundryConnector($accountName, $projectName, $apiKey));
    }

    protected function sendFunctionRuntime(Request $request, string $appName, ?string $hostKey = null): Response
    {
        return $this->send($request, $this->client->functionRuntimeConnector($appName, $hostKey));
    }

    /**
     * @return array<string, mixed>
     */
    protected function jsonArray(Response $response, ?string $key = null): array
    {
        $json = $key === null ? $response->json() : $response->json($key);

        return Field::fromJson($json);
    }

    /**
     * @template TValue
     *
     * @param  callable(array<string, mixed>): TValue  $map
     * @return Collection<int, TValue>
     */
    protected function mapList(Response $response, ?string $key, callable $map): Collection
    {
        $raw = $key === null ? $response->json() : $response->json($key);

        if (! is_array($raw)) {
            return new Collection([]);
        }

        if (array_is_list($raw)) {
            $items = $raw;
        } else {
            $stringKeyRaw = Field::stringKeyArray($raw);
            $value = $stringKeyRaw['value'] ?? [];
            $items = is_array($value) && array_is_list($value) ? $value : [];
        }

        /** @var list<TValue> $mapped */
        $mapped = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $mapped[] = $map(Field::stringKeyArray($item));
            }
        }

        return new Collection($mapped);
    }

    /**
     * Map a paginated ARM/Graph list, following `nextLink` / `@odata.nextLink`
     * until exhausted (or $maxPages is reached, as a runaway safeguard).
     *
     * @template TValue
     *
     * @param  callable(array<string, mixed>): TValue  $map
     * @return Collection<int, TValue>
     */
    protected function mapPaginated(
        Response $response,
        string $key,
        callable $map,
        int $maxPages = 100,
    ): Collection {
        /** @var list<TValue> $all */
        $all = [];
        $current = $response;
        $page = 0;

        while (true) {
            $json = Field::fromJson($current->json());

            $value = $json[$key] ?? [];
            if (is_array($value) && array_is_list($value)) {
                foreach ($value as $item) {
                    if (is_array($item)) {
                        $all[] = $map(Field::stringKeyArray($item));
                    }
                }
            }

            $page++;

            $next = $json['nextLink'] ?? $json['@odata.nextLink'] ?? null;

            if (! is_string($next) || $next === '' || $page >= $maxPages) {
                break;
            }

            $current = $this->send(new GetNextPage($next), $this->client->arm());
        }

        return new Collection($all);
    }

    protected function vaultHost(string $vaultName): string
    {
        return str_contains($vaultName, '.')
            ? $vaultName
            : $vaultName.'.vault.azure.net';
    }
}
