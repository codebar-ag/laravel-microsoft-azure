<?php

use CodebarAg\MicrosoftAzure\Data\Arm\DeletedCognitiveServicesAccountData;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\ListDeletedCognitiveServicesAccounts;
use CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices\PurgeDeletedCognitiveServicesAccount;
use CodebarAg\MicrosoftAzure\Requests\Arm\Support\GetNextPage;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockResponse;

/**
 * Purging is the step that releases a soft-deleted account's model quota, and
 * every assertion here covers a way this family previously got it wrong: it
 * listed from a location-scoped URL ARM does not serve, purged with a POST
 * action verb belonging to the Key Vault family, and read only the first page
 * of a list whose first page is routinely empty.
 */
it('lists soft-deleted accounts across every page, including empty ones', function (): void {
    // ARM really does hand out pages with an empty `value` and a nextLink; a
    // reader that stops at page one concludes the account is already purged
    // and leaves its quota held.
    $client = clientWithArmMock([
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: [
            'value' => [],
            'nextLink' => 'https://management.azure.com/next-page-1',
        ]),
        GetNextPage::class => MockResponse::make(body: [
            'value' => [deletedCognitiveServicesAccountFixture()],
        ]),
    ]);

    $accounts = $client->deletedCognitiveServices('sub-1')->list();

    expect($accounts)->toHaveCount(1)
        ->and($accounts->first())->toBeInstanceOf(DeletedCognitiveServicesAccountData::class)
        ->and($accounts->first()->name)->toBe('aif-test');
});

it('filters the subscription-wide list down to one location', function (): void {
    $client = clientWithArmMock([
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => [
            deletedCognitiveServicesAccountFixture(),
            ['id' => '/subscriptions/sub-1/providers/Microsoft.CognitiveServices/locations/swedencentral/resourceGroups/rg-north/deletedAccounts/aif-north',
                'name' => 'aif-north',
                'location' => 'swedencentral'],
        ]]),
    ]);

    expect($client->deletedCognitiveServices('sub-1')->list('westeurope'))->toHaveCount(1)
        ->and($client->deletedCognitiveServices('sub-1')->list('SwedenCentral')->first()->name)->toBe('aif-north')
        ->and($client->deletedCognitiveServices('sub-1')->list())->toHaveCount(2);
});

it('purges by DELETE on the account path, carrying its already-deleted resource group', function (): void {
    $client = clientWithArmMock([
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: [
            'value' => [deletedCognitiveServicesAccountFixture()],
        ]),
        PurgeDeletedCognitiveServicesAccount::class => MockResponse::make(status: 204),
    ]);

    $account = $client->deletedCognitiveServices('sub-1')->find('westeurope', 'aif-test');

    $client->deletedCognitiveServices('sub-1')->purge($account);

    $request = new PurgeDeletedCognitiveServicesAccount('sub-1', 'westeurope', 'rg-test', 'aif-test');

    expect($request->getMethod())->toBe(Method::DELETE)
        ->and($request->resolveEndpoint())
        ->toBe('/subscriptions/sub-1/providers/Microsoft.CognitiveServices/locations/westeurope/resourceGroups/rg-test/deletedAccounts/aif-test');
});

it('refuses to purge an account whose id carries no resource group', function (): void {
    $client = clientWithArmMock([
        ListDeletedCognitiveServicesAccounts::class => MockResponse::make(body: ['value' => [[
            'id' => '/subscriptions/sub-1/deletedAccounts/aif-test',
            'name' => 'aif-test',
            'location' => 'westeurope',
        ]]]),
    ]);

    $account = $client->deletedCognitiveServices('sub-1')->find('westeurope', 'aif-test');

    // Loud, because the alternative is a purge that never happens and a quota
    // leak that only surfaces when some later, unrelated tenant fails to
    // provision.
    expect(fn () => $client->deletedCognitiveServices('sub-1')->purge($account))
        ->toThrow(InvalidArgumentException::class);
});
