<?php

namespace CodebarAg\MicrosoftAzure\Resources;

use CodebarAg\MicrosoftAzure\Client\AzureClient;
use CodebarAg\MicrosoftAzure\Data\Arm\UsageDetailData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Consumption\ListUsageDetails;
use Illuminate\Support\Collection;

final class ConsumptionResource extends Resource
{
    public function __construct(
        AzureClient $client,
        private readonly string $scope,
    ) {
        parent::__construct($client);
    }

    /**
     * @return Collection<int, UsageDetailData>
     */
    public function usageDetails(?string $filter = null): Collection
    {
        $response = $this->sendArm(new ListUsageDetails($this->scope, $filter));

        return $this->mapPaginated($response, 'value', fn (array $i) => UsageDetailData::fromAzure($i));
    }
}
