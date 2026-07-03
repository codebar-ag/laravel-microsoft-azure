<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Indexes;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class CreateOrUpdateIndexVersion extends FoundryAgentsRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $indexName,
        public readonly string $version,
        public readonly AzurePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/indexes/'.$this->indexName.'/versions/'.$this->version;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
