<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\Invitations;

use CodebarAg\MicrosoftAzure\Data\Payload\CreateInvitationPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateInvitation extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly CreateInvitationPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/invitations';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
