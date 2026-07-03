<?php

namespace CodebarAg\MicrosoftAzure\Requests\Foundry\Skills;

use CodebarAg\MicrosoftAzure\Requests\Foundry\FoundryAgentsRequest;
use Saloon\Enums\Method;

final class GetSkillVersion extends FoundryAgentsRequest
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $skillName,
        public readonly string $version,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/skills/'.$this->skillName.'/versions/'.$this->version;
    }
}
