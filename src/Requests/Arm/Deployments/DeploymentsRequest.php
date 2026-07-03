<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\Deployments;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Http\Request;

/**
 * @internal Shared base for this resource family's requests; not part of the public API.
 */
abstract class DeploymentsRequest extends Request
{
    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_DEPLOYMENTS->value()];
    }
}
