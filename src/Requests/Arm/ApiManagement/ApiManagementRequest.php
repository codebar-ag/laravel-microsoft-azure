<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ApiManagement;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Http\Request;

/**
 * @internal Shared base for API Management ARM requests; not part of the public API.
 */
abstract class ApiManagementRequest extends Request
{
    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_API_MANAGEMENT->value()];
    }
}
