<?php

namespace CodebarAg\MicrosoftAzure\Data\Payload;

/**
 * Body shape confirmed against
 * https://learn.microsoft.com/en-us/rest/api/aifoundry/accountmanagement/project-connections/create
 * — `properties.category`/`authType`/`target`/`credentials`, one of several
 * `ConnectionPropertiesV2` discriminated-by-`authType` shapes. `credentials`
 * is a bare associative array here rather than one of the vendor's own typed
 * shapes (`ConnectionApiKey`, `CustomKeys`, ...) since the exact key name
 * varies per `authType` (`key` for ApiKey/AccountKey, `keys` for CustomKeys,
 * ...) and the caller already knows which `authType` it is building for.
 */
final class ProjectConnectionPayload extends AzurePayload
{
    /**
     * @param  array<string, mixed>|null  $credentials
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public readonly string $category,
        public readonly string $authType,
        public readonly string $target,
        public readonly ?array $credentials = null,
        public readonly array $metadata = [],
    ) {}

    public function toAzureBody(): array
    {
        $properties = [
            'category' => $this->category,
            'authType' => $this->authType,
            'target' => $this->target,
        ];

        if ($this->credentials !== null) {
            $properties['credentials'] = $this->credentials;
        }

        if ($this->metadata !== []) {
            $properties['metadata'] = $this->metadata;
        }

        return ['properties' => $properties];
    }
}
