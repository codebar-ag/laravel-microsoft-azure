<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\CostQueryResultData;
use CodebarAg\MicrosoftAzure\Data\Payload\CostQueryPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\CostManagement\QueryCost;

final class CostManagementResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $scope,
    ) {
        parent::__construct($client);
    }

    public function query(string $from, string $to, string $grouping = 'ServiceName'): CostQueryResultData
    {
        $response = $this->sendArm(new QueryCost(
            $this->scope,
            new CostQueryPayload($from, $to, $grouping),
        ));

        return CostQueryResultData::fromAzure($this->jsonArray($response));
    }
}
