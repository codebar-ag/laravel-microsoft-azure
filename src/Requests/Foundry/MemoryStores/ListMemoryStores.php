<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores;

use Saloon\Enums\Method;

final class ListMemoryStores extends FoundryMemoryStoresRequest
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/memory_stores';
    }
}
