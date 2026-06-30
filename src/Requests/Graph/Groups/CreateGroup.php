<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Groups;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateGroup extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    /**
     * @param  list<string>  $groupTypes
     */
    public function __construct(
        public readonly string $displayName,
        public readonly string $mailNickname,
        public readonly bool $mailEnabled = false,
        public readonly bool $securityEnabled = true,
        public readonly array $groupTypes = ['Unified'],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/groups';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'displayName' => $this->displayName,
            'mailNickname' => $this->mailNickname,
            'mailEnabled' => $this->mailEnabled,
            'securityEnabled' => $this->securityEnabled,
            'groupTypes' => $this->groupTypes,
        ];
    }
}
