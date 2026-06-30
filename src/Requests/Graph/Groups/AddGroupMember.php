<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Groups;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class AddGroupMember extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $groupId,
        public readonly string $memberId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/groups/'.$this->groupId.'/members/$ref';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            '@odata.id' => 'https://graph.microsoft.com/v1.0/directoryObjects/'.$this->memberId,
        ];
    }
}
