<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\CostQueryResultData;
use CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload;
use CodebarAg\MicrosoftAzure\Enums\CostGranularity;
use CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost;

final class CostManagementResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $scope,
    ) {
        parent::__construct($client);
    }

    /**
     * @param  string|list<string>  $grouping  one dimension, or several
     */
    public function query(
        string $from,
        string $to,
        string|array $grouping = 'ServiceName',
        CostGranularity $granularity = CostGranularity::None,
    ): CostQueryResultData {
        $response = $this->sendArm(new QueryCost(
            $this->scope,
            new CostQueryPayload($from, $to, $grouping, $granularity),
        ));

        return CostQueryResultData::fromAzure($this->jsonArray($response));
    }

    /**
     * A day-by-day series for the period — the same query at
     * {@see CostGranularity::Daily}.
     *
     * A named method rather than leaving callers to pass the enum themselves,
     * because the response shape differs rather than merely getting longer:
     * every row gains a `UsageDate` column (`yyyyMMdd`, as an integer), and the
     * row count multiplies by the days in the period. Asking for that by name
     * is a decision; passing a fourth argument is easy to do without noticing
     * what comes back.
     *
     * @param  string|list<string>  $grouping  one dimension, or several
     */
    public function queryDaily(string $from, string $to, string|array $grouping = 'ServiceName'): CostQueryResultData
    {
        return $this->query($from, $to, $grouping, CostGranularity::Daily);
    }
}
