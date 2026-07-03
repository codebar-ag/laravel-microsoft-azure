<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\ModelDeploymentData;
use CodebarAg\MicrosoftAzure\Data\Payload\ModelDeploymentPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\CreateOrUpdateModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\DeleteModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\GetModelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeployments;
use CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices\Deployments\ListModelDeploymentSkus;
use Illuminate\Support\Collection;

final class ModelDeploymentsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroupName,
        private readonly string $accountName,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, ModelDeploymentData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListModelDeployments(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => ModelDeploymentData::fromAzure($item));
    }

    public function get(string $deploymentName): ModelDeploymentData
    {
        $response = $this->sendArm(new GetModelDeployment(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $deploymentName,
        ));

        return ModelDeploymentData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(
        string $deploymentName,
        string $modelFormat,
        string $modelName,
        ?string $modelVersion = null,
        string $skuName = 'GlobalStandard',
        int $skuCapacity = 1,
    ): ModelDeploymentData {
        $response = $this->sendArm(new CreateOrUpdateModelDeployment(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $deploymentName,
            new ModelDeploymentPayload($modelFormat, $modelName, $modelVersion, $skuName, $skuCapacity),
        ));

        return ModelDeploymentData::fromAzure($this->jsonArray($response));
    }

    public function delete(string $deploymentName): void
    {
        $this->sendArm(new DeleteModelDeployment(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $deploymentName,
        ));
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function listSkus(string $deploymentName): Collection
    {
        $response = $this->sendArm(new ListModelDeploymentSkus(
            $this->subscriptionId,
            $this->resourceGroupName,
            $this->accountName,
            $deploymentName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => $item);
    }
}
