<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Data\LogAnalytics\QueryResultsData;
use CodebarAg\MicrosoftAzure\Data\Payload\LogAnalyticsQueryPayload;
use CodebarAg\MicrosoftAzure\Requests\LogAnalytics\ExecuteWorkspaceQuery;

/**
 * KQL query gateway for the Log Analytics data plane.
 *
 * Workspace-based Application Insights resources are queried through the
 * same endpoint using the linked workspace id.
 */
final class LogAnalyticsQueryResource extends Resource
{
    public function query(string $workspaceId, string $kql, ?string $timespan = null): QueryResultsData
    {
        $response = $this->sendLogAnalytics(new ExecuteWorkspaceQuery(
            $workspaceId,
            new LogAnalyticsQueryPayload($kql, $timespan),
        ));

        return QueryResultsData::fromAzure($this->jsonArray($response));
    }
}
