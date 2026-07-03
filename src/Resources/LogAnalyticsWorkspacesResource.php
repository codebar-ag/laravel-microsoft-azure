<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\LogAnalyticsWorkspaceData;
use CodebarAg\MicrosoftAzure\Requests\Arm\OperationalInsights\ListWorkspacesByResourceGroup;
use Illuminate\Support\Collection;

final class LogAnalyticsWorkspacesResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $subscriptionId,
        private readonly string $resourceGroup,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, LogAnalyticsWorkspaceData>
     */
    public function list(): Collection
    {
        $response = $this->sendArm(new ListWorkspacesByResourceGroup(
            $this->subscriptionId,
            $this->resourceGroup,
        ));

        return $this->mapList($response, 'value', fn (array $item) => LogAnalyticsWorkspaceData::fromAzure($item));
    }

    public function workspace(string $workspaceName): LogAnalyticsWorkspaceResource
    {
        return new LogAnalyticsWorkspaceResource(
            $this->client,
            $this->subscriptionId,
            $this->resourceGroup,
            $workspaceName,
        );
    }

    /**
     * @param  array<string, mixed>  $properties
     * @param  array<string, string>  $tags
     */
    public function createOrUpdate(
        string $workspaceName,
        string $location,
        string $skuName = 'PerGB2018',
        int $retentionInDays = 30,
        array $properties = [],
        array $tags = [],
    ): LogAnalyticsWorkspaceData {
        return $this->workspace($workspaceName)->createOrUpdate($location, $skuName, $retentionInDays, $properties, $tags);
    }
}
