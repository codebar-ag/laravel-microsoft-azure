<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Responses;

use CodebarAg\MicrosoftAzure\Concerns\SendsJsonObjectBody;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;

final class CancelResponse extends FoundryAgentsRequest implements HasBody
{
    use SendsJsonObjectBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $responseId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/responses/'.$this->responseId.'/cancel';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [];
    }
}
