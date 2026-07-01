<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Agents;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteAgentVersion extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $agentName,
        public readonly string $version,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents/'.$this->agentName.'/versions/'.$this->version;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::FOUNDRY_AGENTS];
    }
}
