<?php

use CodebarAg\MicrosoftAzure\Data\Arm\UsageDetailData;
use CodebarAg\MicrosoftAzure\Requests\Arm\Consumption\ListUsageDetails;
use CodebarAg\MicrosoftAzure\Requests\Arm\Support\GetNextPage;
use CodebarAg\MicrosoftAzure\Resources\ConsumptionResource;
use Saloon\Http\Faking\MockResponse;

it('maps paginated usage details across pages', function (): void {
    $client = clientWithArmMock([
        ListUsageDetails::class => MockResponse::make(body: [
            'value' => [
                [
                    'id' => '/subscriptions/sub-1/usage/1',
                    'name' => 'usage-1',
                    'properties' => [
                        'cost' => 12.5,
                        'billingCurrency' => 'CHF',
                        'date' => '2026-01-01',
                        'product' => 'Storage - GRS',
                        'meterDetails' => ['meterName' => 'GRS Data Stored'],
                    ],
                ],
            ],
            'nextLink' => 'https://management.azure.com/subscriptions/sub-1/providers/Microsoft.Consumption/usageDetails?api-version=2023-05-01&$skiptoken=abc',
        ]),
        GetNextPage::class => MockResponse::make(body: [
            'value' => [
                [
                    'id' => '/subscriptions/sub-1/usage/2',
                    'name' => 'usage-2',
                    'properties' => [
                        'costInBillingCurrency' => 4.25,
                        'currency' => 'CHF',
                        'date' => '2026-01-02',
                        'product' => 'Compute',
                    ],
                ],
            ],
        ]),
    ]);

    $usage = (new ConsumptionResource($client, 'subscriptions/sub-1'))->usageDetails();

    expect($usage)->toHaveCount(2);

    $first = $usage->first();

    expect($first)->toBeInstanceOf(UsageDetailData::class)
        ->and($first->cost)->toBe(12.5)
        ->and($first->currency)->toBe('CHF')
        ->and($first->date)->toBe('2026-01-01')
        ->and($first->product)->toBe('Storage - GRS')
        ->and($first->meterName)->toBe('GRS Data Stored');

    $second = $usage->last();

    expect($second->cost)->toBe(4.25)
        ->and($second->currency)->toBe('CHF')
        ->and($second->meterName)->toBeNull();
});
