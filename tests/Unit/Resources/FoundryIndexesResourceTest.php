<?php

use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\CreateOrUpdateIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\DeleteIndexVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes\GetIndexVersion;
use CodebarAg\MicrosoftAzure\Resources\FoundryIndexesResource;
use Saloon\Http\Faking\MockResponse;

it('creates or updates, gets and deletes an index version', function (): void {
    $client = clientWithFoundryMock([
        CreateOrUpdateIndexVersion::class => MockResponse::make(body: ['name' => 'invoices-index', 'version' => '1']),
        GetIndexVersion::class => MockResponse::make(body: ['name' => 'invoices-index', 'version' => '1']),
        DeleteIndexVersion::class => MockResponse::make(status: 204),
    ]);

    $indexes = $client->foundry('my-foundry', 'default')->indexes();

    expect($indexes)->toBeInstanceOf(FoundryIndexesResource::class)
        ->and($indexes->createOrUpdateVersion('invoices-index', '1', ['dataset' => 'invoices']))->toHaveKey('version', '1')
        ->and($indexes->getVersion('invoices-index', '1'))->toHaveKey('version', '1');

    $indexes->deleteVersion('invoices-index', '1');
});
