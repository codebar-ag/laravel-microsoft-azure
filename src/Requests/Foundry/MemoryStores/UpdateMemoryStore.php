<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\MemoryStores;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class UpdateMemoryStore extends FoundryMemoryStoresRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public readonly string $memoryStoreId,
        public readonly AzurePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/memory_stores/'.$this->memoryStoreId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
