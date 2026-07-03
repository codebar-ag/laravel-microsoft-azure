<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Settings;

use CodebarAg\MicrosoftAzure\Data\Payload\AppSettingsPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class UpdateApplicationSettings extends WebRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
        public readonly AppSettingsPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'/config/appsettings';
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
