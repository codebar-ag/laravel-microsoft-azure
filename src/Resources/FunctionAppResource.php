<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\FunctionData;
use CodebarAg\MicrosoftAzure\Data\Arm\WebSiteData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\WebSitePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Functions\ListFunctions;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\CreateOrUpdateSiteConfig;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\DeleteSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\GetSiteConfig;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\RestartSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StartSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites\StopSite;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\ListSyncFunctionTriggersStatus;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\Triggers\SyncFunctionTriggers;
use Illuminate\Support\Collection;

final class FunctionAppResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $appName,
    ) {
        parent::__construct($client);
    }

    public function get(): WebSiteData
    {
        $response = $this->sendArm(new GetSite(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return WebSiteData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        array $properties = [],
        array $tags = [],
        string $kind = 'functionapp',
    ): WebSiteData {
        $response = $this->sendArm(new CreateOrUpdateSite(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            new WebSitePayload($location, $kind, $properties, $tags),
        ));

        return WebSiteData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteSite(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));
    }

    public function restart(): void
    {
        $this->sendArm(new RestartSite(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));
    }

    public function start(): void
    {
        $this->sendArm(new StartSite(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));
    }

    public function stop(): void
    {
        $this->sendArm(new StopSite(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        $response = $this->sendArm(new GetSiteConfig(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return $this->jsonArray($response);
    }

    /**
     * @param  array<string, mixed>  $config
     * @return array<string, mixed>
     */
    public function createOrUpdateConfig(array $config): array
    {
        $response = $this->sendArm(new CreateOrUpdateSiteConfig(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            new GenericJsonPayload($config),
        ));

        return $this->jsonArray($response);
    }

    public function settings(): FunctionAppSettingsResource
    {
        return new FunctionAppSettingsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        );
    }

    public function hostKeys(): FunctionAppHostKeysResource
    {
        return new FunctionAppHostKeysResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        );
    }

    public function functions(string $functionName): FunctionResource
    {
        return new FunctionResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
            $functionName,
        );
    }

    /**
     * @return Collection<int, FunctionData>
     */
    public function listFunctions(): Collection
    {
        $response = $this->sendArm(new ListFunctions(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => FunctionData::fromAzure($item));
    }

    public function syncTriggers(): void
    {
        $this->sendArm(new SyncFunctionTriggers(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));
    }

    /**
     * @return array<string, mixed>
     */
    public function syncTriggersStatus(): array
    {
        $response = $this->sendArm(new ListSyncFunctionTriggersStatus(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->appName,
        ));

        return $this->jsonArray($response);
    }
}
