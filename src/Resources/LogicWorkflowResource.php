<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicCallbackUrlData;
use CodebarAg\MicrosoftAzure\Data\Arm\LogicWorkflowData;
use CodebarAg\MicrosoftAzure\Data\Payload\GenericJsonPayload;
use CodebarAg\MicrosoftAzure\Data\Payload\LogicWorkflowPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\CreateOrUpdateLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DeleteLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\DisableLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\EnableLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GenerateUpgradedDefinition;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\GetLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ListLogicWorkflowCallbackUrl;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\RegenerateLogicWorkflowAccessKey;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\UpdateLogicWorkflow;
use CodebarAg\MicrosoftAzure\Requests\Arm\Logic\Workflows\ValidateLogicWorkflow;

final class LogicWorkflowResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $workflowName,
    ) {
        parent::__construct($client);
    }

    public function get(): LogicWorkflowData
    {
        $response = $this->sendArm(new GetLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));

        return LogicWorkflowData::fromAzure($this->jsonArray($response));
    }

    public function createOrUpdate(LogicWorkflowPayload $payload): LogicWorkflowData
    {
        $response = $this->sendArm(new CreateOrUpdateLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $payload,
        ));

        return LogicWorkflowData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $patch
     */
    public function update(array $patch): LogicWorkflowData
    {
        $response = $this->sendArm(new UpdateLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            new GenericJsonPayload($patch),
        ));

        return LogicWorkflowData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));
    }

    public function enable(): void
    {
        $this->sendArm(new EnableLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));
    }

    public function disable(): void
    {
        $this->sendArm(new DisableLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));
    }

    public function listCallbackUrl(): LogicCallbackUrlData
    {
        $response = $this->sendArm(new ListLogicWorkflowCallbackUrl(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        ));

        return LogicCallbackUrlData::fromAzure($this->jsonArray($response));
    }

    /**
     * @return array<string, mixed>
     */
    public function generateUpgradedDefinition(string $targetSchemaVersion = '2016-06-01'): array
    {
        $response = $this->sendArm(new GenerateUpgradedDefinition(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            new GenericJsonPayload(['targetSchemaVersion' => $targetSchemaVersion]),
        ));

        return $this->jsonArray($response);
    }

    public function regenerateAccessKey(string $keyType = 'Primary'): void
    {
        $this->sendArm(new RegenerateLogicWorkflowAccessKey(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            new GenericJsonPayload(['keyType' => $keyType]),
        ));
    }

    public function validate(LogicWorkflowPayload $payload): void
    {
        $this->sendArm(new ValidateLogicWorkflow(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
            $payload,
        ));
    }

    public function versions(): LogicWorkflowVersionsResource
    {
        return new LogicWorkflowVersionsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        );
    }

    public function triggers(): LogicWorkflowTriggersResource
    {
        return new LogicWorkflowTriggersResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        );
    }

    public function runs(): LogicWorkflowRunsResource
    {
        return new LogicWorkflowRunsResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workflowName,
        );
    }
}
