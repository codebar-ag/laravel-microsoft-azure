<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites;

use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Turns the site's SCM or FTP basic-auth publishing credentials on or off
 * (`$policyName` is `scm` or `ftp`). An app reached only through a managed
 * identity has no use for either, and they are on by default.
 */
final class UpdateBasicPublishingCredentialsPolicy extends WebRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
        public readonly string $policyName,
        public readonly bool $allow,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'/basicPublishingCredentialsPolicies/'.$this->policyName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return ['properties' => ['allow' => $this->allow]];
    }
}
