<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Concerns\HandlesLongRunningOperations;
use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentData;
use CodebarAg\MicrosoftAzure\Data\Arm\DeploymentOperationData;
use CodebarAg\MicrosoftAzure\Data\Payload\DeploymentPayload;
use CodebarAg\MicrosoftAzure\Enums\DeploymentMode;
use CodebarAg\MicrosoftAzure\Enums\ProvisioningState;
use CodebarAg\MicrosoftAzure\Exceptions\DeploymentFailedException;
use CodebarAg\MicrosoftAzure\Exceptions\LongRunningOperationException;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CancelDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\CreateOrUpdateDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\GetDeployment;
use CodebarAg\MicrosoftAzure\Requests\Arm\Deployments\ListDeploymentOperations;
use Illuminate\Support\Collection;

final class DeploymentsResource extends Resource
{
    use HandlesLongRunningOperations;

    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
    ) {
        parent::__construct($client);
    }

    /**
     * Poll the deployment until its provisioningState is terminal.
     *
     * @param  (callable(DeploymentData): void)|null  $onTick
     *
     * @throws LongRunningOperationException
     */
    public function await(
        string $deploymentName,
        int $timeoutSeconds = 600,
        int $intervalSeconds = 5,
        ?callable $onTick = null,
    ): DeploymentData {
        /** @var DeploymentData $deployment */
        $deployment = $this->awaitProvisioningState(
            fn (): DeploymentData => $this->get($deploymentName),
            $timeoutSeconds,
            $intervalSeconds,
            $onTick,
        );

        return $deployment;
    }

    /**
     * @param  array<string, mixed>  $template
     * @param  array<string, mixed>  $parameters
     */
    public function createOrUpdate(
        string $deploymentName,
        array $template,
        array $parameters = [],
        DeploymentMode $mode = DeploymentMode::Incremental,
    ): DeploymentData {
        $response = $this->sendArm(new CreateOrUpdateDeployment(
            $this->subscriptionId,
            $this->resourceGroup,
            $deploymentName,
            new DeploymentPayload($template, $parameters, $mode),
        ));

        $deployment = DeploymentData::fromAzure($this->jsonArray($response));

        if ($deployment->provisioningState === ProvisioningState::Failed) {
            throw new DeploymentFailedException(
                "Deployment [{$deploymentName}] failed.",
                null,
                $this->client->name(),
            );
        }

        return $deployment;
    }

    public function get(string $deploymentName): DeploymentData
    {
        $response = $this->sendArm(new GetDeployment(
            $this->subscriptionId,
            $this->resourceGroup,
            $deploymentName,
        ));

        return DeploymentData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return Collection<int, DeploymentOperationData>
     */
    public function operations(string $deploymentName): Collection
    {
        $response = $this->sendArm(new ListDeploymentOperations(
            $this->subscriptionId,
            $this->resourceGroup,
            $deploymentName,
        ));

        return $this->mapList($response, 'value', fn (array $item) => DeploymentOperationData::fromAzure($item));
    }

    public function cancel(string $deploymentName): void
    {
        $this->sendArm(new CancelDeployment(
            $this->subscriptionId,
            $this->resourceGroup,
            $deploymentName,
        ));
    }
}
