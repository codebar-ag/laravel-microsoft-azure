<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\RoleDefinitions;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ListRoleDefinitions extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $subscriptionId,
        public readonly ?string $filter = null,
    ) {}

    public function resolveEndpoint(): string
    {
        return '/subscriptions/'.$this->subscriptionId.'/providers/Microsoft.Authorization/roleDefinitions';
    }

    protected function defaultQuery(): array
    {
        $query = ['api-version' => ApiVersion::ARM_ROLE_DEFINITIONS];

        if ($this->filter !== null && $this->filter !== '') {
            $query['$filter'] = $this->filter;
        }

        return $query;
    }
}
