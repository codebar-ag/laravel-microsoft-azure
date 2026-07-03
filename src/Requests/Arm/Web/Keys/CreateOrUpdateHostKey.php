<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Web\Keys;

use CodebarAg\MicrosoftAzure\Data\Payload\FunctionKeyPayload;
use CodebarAg\MicrosoftAzure\Requests\Arm\Web\WebRequest;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateHostKey extends WebRequest implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $appName,
        public readonly string $keyName,
        public readonly FunctionKeyPayload $payload,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/resourceGroups/'.$this->resourceGroupName
            .'/providers/Microsoft.Web/sites/'.$this->appName
            .'/host/default/keys/'.$this->keyName;
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return $this->payload->toAzureBody();
    }
}
