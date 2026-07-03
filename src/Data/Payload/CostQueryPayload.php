<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

final class CostQueryPayload extends AzurePayload
{
    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly string $grouping = 'ServiceName',
    ) {}

    public function toAzureBody(): array
    {
        return [
            'type' => 'ActualCost',
            'timeframe' => 'Custom',
            'timePeriod' => [
                'from' => $this->from,
                'to' => $this->to,
            ],
            'dataset' => [
                'granularity' => 'None',
                'aggregation' => [
                    'totalCost' => [
                        'name' => 'Cost',
                        'function' => 'Sum',
                    ],
                ],
                'grouping' => [
                    [
                        'type' => 'Dimension',
                        'name' => $this->grouping,
                    ],
                ],
            ],
        ];
    }
}
