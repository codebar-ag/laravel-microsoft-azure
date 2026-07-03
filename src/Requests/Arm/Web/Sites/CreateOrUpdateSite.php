<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Sites;

use CodebarAg\MicrosoftAzure\Data\Payload\WebSitePayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateSite extends WebRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
        public readonly WebSitePayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
