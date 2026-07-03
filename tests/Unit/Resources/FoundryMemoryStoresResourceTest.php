<?php

use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\CreateMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\DeleteMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\GetMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\ListMemoryStores;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\SearchMemoryStore;
use CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores\UpdateMemoryStore;
use CodebarAg\MicrosoftAzure\Resources\FoundryMemoryStoresResource;
use Saloon\Http\Faking\MockResponse;

it('creates, lists, gets, updates, searches and deletes memory stores', function (): void {
    $client = clientWithFoundryMock([
        CreateMemoryStore::class => MockResponse::make(body: ['id' => 'ms-1']),
        ListMemoryStores::class => MockResponse::make(body: ['data' => [['id' => 'ms-1']]]),
        GetMemoryStore::class => MockResponse::make(body: ['id' => 'ms-1', 'status' => 'ready']),
        UpdateMemoryStore::class => MockResponse::make(body: ['id' => 'ms-1', 'status' => 'extracting']),
        SearchMemoryStore::class => MockResponse::make(body: ['results' => [['text' => 'last order status']]]),
        DeleteMemoryStore::class => MockResponse::make(status: 204),
    ]);

    $memoryStores = $client->foundry('my-foundry', 'default')->memoryStores();

    expect($memoryStores)->toBeInstanceOf(FoundryMemoryStoresResource::class)
        ->and($memoryStores->create([]))->toHaveKey('id', 'ms-1')
        ->and($memoryStores->list())->toHaveCount(1)
        ->and($memoryStores->get('ms-1'))->toHaveKey('status', 'ready')
        ->and($memoryStores->update('ms-1', ['conversation_id' => 'conv-1']))->toHaveKey('status', 'extracting')
        ->and($memoryStores->search('ms-1', ['query' => 'last order status']))->toHaveKey('results');

    $memoryStores->delete('ms-1');
});
