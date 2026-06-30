<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ApplicationInsightsComponentData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\ListComponentsByResourceGroup;
use Illuminate\Support\Collection;

final class ApplicationInsightsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, ApplicationInsightsComponentData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListComponentsByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroup,
        ));

        return $this->mapList($response, 'value', fn (array $item) => ApplicationInsightsComponentData::fromAzure($item));
    }

    public function component(string $componentName): ApplicationInsightsComponentResource
    {
        return new ApplicationInsightsComponentResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $componentName,
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $componentName,
        string $location,
        string $applicationType = 'web',
        string $kind = 'web',
        ?string $workspaceResourceId = null,
        array $properties = [],
        array $tags = [],
    ): ApplicationInsightsComponentData {
        return $this->component($componentName)->createOrUpdate($location, $applicationType, $kind, $workspaceResourceId, $properties, $tags);
    }
}
