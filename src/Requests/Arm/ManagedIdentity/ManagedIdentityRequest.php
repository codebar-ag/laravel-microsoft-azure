<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\ManagedIdentity;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Http\Request;

/**
 * @internal Shared base for this resource family's requests; not part of the public API.
 */
abstract class ManagedIdentityRequest extends Request
{
    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_MANAGED_IDENTITY->value()];
    }
}
