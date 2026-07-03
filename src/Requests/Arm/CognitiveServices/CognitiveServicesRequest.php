<?php

namespace CodebarAg\MicrosoftAzure\Requests\Arm\CognitiveServices;

use CodebarAg\MicrosoftAzure\Enums\ApiVersion;
use Saloon\Http\Request;

/**
 * @internal Shared base for this resource family's requests; not part of the public API.
 */
abstract class CognitiveServicesRequest extends Request
{
    /** @return array<string, mixed> */
    protected function defaultQuery(): array
    {
        return ['api-version' => ApiVersion::ARM_COGNITIVE_SERVICES->value()];
    }
}
