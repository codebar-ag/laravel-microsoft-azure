<?php

use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\CreateOrUpdateDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\DeleteDatasetVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Datasets\GetDatasetVersion;
use CodebarAg\MicrosoftAzure\Resources\FoundryDatasetsResource;
use Saloon\Http\Faking\MockResponse;

it('creates or updates, gets and deletes a dataset version', function (): void {
    $client = clientWithFoundryMock([
        CreateOrUpdateDatasetVersion::class => MockResponse::make(body: ['name' => 'invoices', 'version' => '1']),
        GetDatasetVersion::class => MockResponse::make(body: ['name' => 'invoices', 'version' => '1']),
        DeleteDatasetVersion::class => MockResponse::make(status: 204),
    ]);

    $datasets = $client->foundry('my-foundry', 'default')->datasets();

    expect($datasets)->toBeInstanceOf(FoundryDatasetsResource::class)
        ->and($datasets->createOrUpdateVersion('invoices', '1', ['source' => 'blob://...']))->toHaveKey('version', '1')
        ->and($datasets->getVersion('invoices', '1'))->toHaveKey('version', '1');

    $datasets->deleteVersion('invoices', '1');
});
