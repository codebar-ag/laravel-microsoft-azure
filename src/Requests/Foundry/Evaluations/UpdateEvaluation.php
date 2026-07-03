<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Evaluations;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Data\Payload\AzurePayload;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class UpdateEvaluation extends FoundryAgentsRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::PATCH;

    public function __construct(
        public readonly string $evaluationId,
        public readonly AzurePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/evaluations/'.$this->evaluationId;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
