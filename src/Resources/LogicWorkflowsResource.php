<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowData;
use CodebarAg\MicrosoftAzure\Data\Payload\LogicWorkflowPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsByResourceGroup;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowsBySubscription;
use Illuminate\Support\Collection;

final class LogicWorkflowsResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, LogicWorkflowData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowsByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroup,
        ));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowData::fromAzure($item));
    }

    /**
     * @return Collection<int, LogicWorkflowData>
     */
    public function listBySubscription(): Collection
    {
        $response = $this->sendArm(new ListLogicWorkflowsBySubscription($this->subscriptionId));

        return $this->mapPaginated($response, 'value', fn (array $item) => LogicWorkflowData::fromAzure($item));
    }

    public function workflow(string $workflowName): LogicWorkflowResource
    {
        return new LogicWorkflowResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $workflowName,
        );
    }

    /**
     * @param  array<string, mixed>  $definition
     * @param  array<string, mixed>  $parameters
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $workflowName,
        string $location,
        array $definition,
        array $parameters = [],
        ?string $state = null,
        ?string $integrationAccountId = null,
        array $tags = [],
    ): LogicWorkflowData {
        return $this->workflow($workflowName)->createOrUpdate(
            new LogicWorkflowPayload($location, $definition, $parameters, $state, $integrationAccountId, $tags),
        );
    }
}
