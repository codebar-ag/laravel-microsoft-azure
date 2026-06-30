<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Agents;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class GetAgent extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $agentId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents/'.$this->agentId;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::FOUNDRY_AGENTS];
    }
}
