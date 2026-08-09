<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\ObservesActualState;
use CodebarAg\MicrosoftAzure\Data\Arm\ServerFarmData;
use CodebarAg\MicrosoftAzure\Data\Observation\ResourceObservationData;
use CodebarAg\MicrosoftAzure\Data\Payload\ServerFarmPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\ServerFarms\CreateOrUpdateServerFarm;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\ServerFarms\GetServerFarm;

final class ServerFarmResource extends Resource
{
    use ObservesActualState;

    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $planName,
    ) {
        parent::__construct($client);
    }

    public function get(): ServerFarmData
    {
        $response = $this->sendArm(new GetServerFarm(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->planName,
        ));

        return ServerFarmData::fromAzure($this->jsonArray($response));
    }

    /**
     * Actual-state read — the one a create should be guarded by. A plan cannot
     * change tier or region in place, and plan creation is quota-limited per
     * subscription, so "does this already exist and is it the plan I wanted"
     * is a different question from "create it".
     */
    public function observe(): ResourceObservationData
    {
        return $this->observeArm(
            new GetServerFarm(
                $this->subscriptionId,
                $this->resourceGroupName,
                $this->planName,
            ),
            fn (array $json): ServerFarmData => ServerFarmData::fromAzure($json),
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        string $skuName = 'Y1',
        string $skuTier = 'Dynamic',
        ?string $kind = null,
        array $properties = [],
        array $tags = [],
    ): ServerFarmData {
        $response = $this->sendArm(new CreateOrUpdateServerFarm(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->planName,
            new ServerFarmPayload($location, $skuName, $skuTier, $kind, $properties, $tags),
        ));

        return ServerFarmData::fromAzure($this->jsonArray($response));
    }
}
