<?php

namespace CodebarAg\MicrosoftAzure\Requests\Graph\ServicePrincipals;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteServicePrincipal extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(
        public readonly string $servicePrincipalId,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/servicePrincipals/'.$this->servicePrincipalId;
    }
}
