<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Toolboxes;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Contracts\FoundryFeatureRequest;
use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class UpdateToolbox extends FoundryAgentsRequest implements FoundryFeatureRequest, HasBody
{
    use HasFoundryFeatures;
    use SendsJsonObjectBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public readonly string $toolboxName,
        public readonly AzurePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/toolboxes/'.$this->toolboxName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
