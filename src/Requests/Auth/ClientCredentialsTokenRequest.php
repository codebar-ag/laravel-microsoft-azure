<?php

namespace CodebarAg\MicrosoftAzure\Requests\Auth;

use CodebarAg\MicrosoftAzure\Data\Payload\ClientCredentialsTokenPayload;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasFormBody;

final class ClientCredentialsTokenRequest extends Request implements HasBody
{
    use HasFormBody;

    protected Method $method = Method::POST;

    public function __construct(
        public readonly string $tenantId,
        public readonly ClientCredentialsTokenPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/'.$this->tenantId.'/oauth2/v2.0/token';
    }

    public function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
        ];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toFormBody();
    }
}
