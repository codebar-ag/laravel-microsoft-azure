<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Agents;

use CodebarAg\MicrosoftAzure\Concerns\HasFoundryFeatures;
use CodebarAg\MicrosoftAzure\Enums\AgentKind;
use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class ListAgents extends FoundryAgentsRequest
{
    use HasFoundryFeatures;

    protected Method $method = Method::GET;

    public function __construct(
        public readonly ?AgentKind $kind = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/agents';
    }

    protected function defaultQuery(): array
    {
        $query = ['api-version' => ApiVersion::FOUNDRY_AGENTS->value()];

        if ($this->kind !== null) {
            $query['kind'] = $this->kind->value;
        }

        return $query;
    }
}
