<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ResourceGroups;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

final class CreateOrUpdateResourceGroup extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::PUT;

    /**
     * @param  array<string, mixed>  $properties
     */
    public function __construct(
        public readonly string $subscriptionId,
        public readonly string $resourceGroupName,
        public readonly string $location,
        public readonly array $properties = [],
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/resourcegroups/'.$this->resourceGroupName;
    }

    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_RESOURCES];
    }

    /** @return array<string, mixed> */
    protected function defaultBody(): array
    {
        return [
            'location' => $this->location,
            'properties' => $this->properties,
        ];
    }
}
