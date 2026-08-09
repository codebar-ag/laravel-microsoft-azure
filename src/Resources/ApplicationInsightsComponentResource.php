<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\ObservesActualState;
use CodebarAg\MicrosoftAzure\Data\Arm\ApplicationInsightsComponentData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Data\Payload\ApplicationInsightsComponentPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\CreateOrUpdateComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\DeleteComponent;
use CodebarAg\MicrosoftAzure\Requests\Arm\Insights\Components\GetComponent;

final class ApplicationInsightsComponentResource extends Resource
{
    use ObservesActualState;

    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $componentName,
    ) {
        parent::__construct($client);
    }

    public function get(): ApplicationInsightsComponentData
    {
        $response = $this->sendArm(new GetComponent(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->componentName,
        ));

        return ApplicationInsightsComponentData::fromAzure($this->jsonArray($response));
    }

    /**
     * Actual-state read.
     *
     * The observation reads `properties.provisioningState` from the raw body
     * rather than from the DTO, which types that field as a plain string for
     * this provider — so the state mapping is the same as everywhere else.
     */
    public function observe(): ResourceObservationData
    {
        return $this->observeArm(
            new GetComponent(
                $this->subscriptionId,
                $this->resourceGroup,
                $this->componentName,
            ),
            fn (array $json): ApplicationInsightsComponentData => ApplicationInsightsComponentData::fromAzure($json),
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        string $applicationType = 'web',
        string $kind = 'web',
        ?string $workspaceResourceId = null,
        array $properties = [],
        array $tags = [],
    ): ApplicationInsightsComponentData {
        $response = $this->sendArm(new CreateOrUpdateComponent(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->componentName,
            new ApplicationInsightsComponentPayload($location, $applicationType, $kind, $workspaceResourceId, $properties, $tags),
        ));

        return ApplicationInsightsComponentData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteComponent(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->componentName,
        ));
    }
}
