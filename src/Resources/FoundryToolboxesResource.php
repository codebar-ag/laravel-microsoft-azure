<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CallToolboxMcpTool;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CreateToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\CreateToolboxVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\DeleteToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\GetToolbox;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxes;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxMcpTools;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\ListToolboxVersions;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes\UpdateToolbox;
use Illuminate\Support\Collection;

final class FoundryToolboxesResource extends FoundryScopedResource
{
    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function list(): Collection
    {
        $response = $this->dispatchFoundry(new ListToolboxes);

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function create(array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new CreateToolbox($this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function get(string $toolboxName): array
    {
        $response = $this->dispatchFoundry(new GetToolbox($toolboxName));

        return $this->jsonArray($response);
    }

    public function delete(string $toolboxName): void
    {
        $this->dispatchFoundry(new DeleteToolbox($toolboxName));
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     * @return array<string, mixed>
     */
    public function createVersion(string $toolboxName, array|AzurePayload $body): array
    {
        $response = $this->dispatchFoundry(new CreateToolboxVersion($toolboxName, $this->resolvePayload($body)));

        return $this->jsonArray($response);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listVersions(string $toolboxName): Collection
    {
        $response = $this->dispatchFoundry(new ListToolboxVersions($toolboxName));

        return $this->mapList($response, 'data', fn (array $item) => $item);
    }

    /** @return array<string, mixed> */
    public function setDefaultVersion(string $toolboxName, string $version): array
    {
        $response = $this->dispatchFoundry(new UpdateToolbox(
            $toolboxName,
            new GenericJsonPayload(['default_version' => $version]),
        ));

        return $this->jsonArray($response);
    }

    /** @return array<string, mixed> */
    public function listMcpTools(string $toolboxName, string $version): array
    {
        $response = $this->dispatchFoundry(new ListToolboxMcpTools($toolboxName, $version));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    public function callMcpTool(string $toolboxName, string $version, string $toolName, array $arguments = []): array
    {
        $response = $this->dispatchFoundry(new CallToolboxMcpTool($toolboxName, $version, $toolName, $arguments));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>|AzurePayload  $body
     */
    private function resolvePayload(array|AzurePayload $body): AzurePayload
    {
        return $body instanceof AzurePayload ? $body : new GenericJsonPayload($body);
    }
}
