<?php

namespace CodebarAg\MicrosoftAzure\Concerns;

use CodebarAg\MicrosoftAzure\Enums\FoundryFeature;
use Saloon\Http\Request;

trait HasFoundryFeatures
{
    /** @var list<FoundryFeature>|null */
    private ?array $foundryFeatures = null;

    /**
     * @param  list<FoundryFeature>  $features
     */
    public function withFoundryFeatures(array $features): Request
    {
        $clone = clone $this;
        $clone->foundryFeatures = $features;

        return $clone;
    }

    /**
     * @return array<string, string>
     */
    protected function defaultHeaders(): array
    {
        if ($this->foundryFeatures === null || $this->foundryFeatures === []) {
            return [];
        }

        return [
            'Foundry-Features' => FoundryFeature::toHeader($this->foundryFeatures),
        ];
    }
}
