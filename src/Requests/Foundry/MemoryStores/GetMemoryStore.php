<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores;

use Saloon\Enums\Method;

final class GetMemoryStore extends FoundryMemoryStoresRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $memoryStoreId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/memory_stores/'.$this->memoryStoreId;
    }
}
