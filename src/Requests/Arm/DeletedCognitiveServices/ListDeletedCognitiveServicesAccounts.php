<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\DeletedCognitiveServices;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListDeletedCognitiveServicesAccounts extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $location,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId
            .'/providers/Microsoft.CognitiveServices/locations/'.$this->location.'/deletedAccounts';
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DELETED_COGNITIVE_SERVICES];
    }
}
