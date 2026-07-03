<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Groups;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class RemoveGroupMember extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $groupId,
        public readonly string $memberId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/groups/'.$this->groupId.'/members/'.$this->memberId.'/$ref';
    }
}
