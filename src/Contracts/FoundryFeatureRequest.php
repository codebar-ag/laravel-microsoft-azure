<?php

namespace CodebarAg\MicrosoftAzure\Contracts;

use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use Saloon\Http\Request;

/**
 * @phpstan-require-extends Request
 */
interface FoundryFeatureRequest
{
    /**
     * @param  list<FoundryFeature>  $features
     */
    public function withFoundryFeatures(array $features): Request;
}
