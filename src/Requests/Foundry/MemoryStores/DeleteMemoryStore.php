<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores;

use Saloon\Enums\Method;

final class DeleteMemoryStore extends FoundryMemoryStoresRequest
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $memoryStoreId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/memory_stores/'.$this->memoryStoreId;
    }
}
