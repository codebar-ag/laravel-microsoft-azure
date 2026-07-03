<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogAnalyticsWorkspaceData;
use CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsWorkspacePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\CreateOrUpdateWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\DeleteWorkspace;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\GetWorkspace;

final class LogAnalyticsWorkspaceResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
        private readonly string $workspaceName,
    ) {
        parent::__construct($client);
    }

    public function get(): LogAnalyticsWorkspaceData
    {
        $response = $this->sendArm(new GetWorkspace(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workspaceName,
        ));

        return LogAnalyticsWorkspaceData::fromAzure($this->jsonArray($response));
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $location,
        string $skuName = 'PerGB2018',
        int $retentionInDays = 30,
        array $properties = [],
        array $tags = [],
    ): LogAnalyticsWorkspaceData {
        $response = $this->sendArm(new CreateOrUpdateWorkspace(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workspaceName,
            new LogAnalyticsWorkspacePayload($location, $skuName, $retentionInDays, $properties, $tags),
        ));

        return LogAnalyticsWorkspaceData::fromAzure($this->jsonArray($response));
    }

    public function delete(): void
    {
        $this->sendArm(new DeleteWorkspace(
            $this->subscriptionId,
            $this->resourceGroup,
            $this->workspaceName,
        ));
    }
}
